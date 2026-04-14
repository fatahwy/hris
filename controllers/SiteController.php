<?php

namespace app\controllers;

use app\helpers\DBHelper;
use app\helpers\GeneralHelper;
use app\helpers\RoleHelper;
use app\models\master\Company;
use Yii;
use yii\bootstrap5\Html;
use yii\web\Response;
use app\models\LoginForm;
use app\models\RegisterForm;
use app\models\master\Account;

class SiteController extends BaseController
{

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage/Dashboard.
     *
     * @return string
     */
    public function actionIndex()
    {
        $id_company = $this->id_company;
        
        $dateRange = Yii::$app->request->get('date_range');
        if ($dateRange) {
            $dates = explode(' - ', $dateRange);
            $startDate = $dates[0];
            $endDate = $dates[1];
        } else {
            $startDate = date('Y-m-01');
            $endDate = date('Y-m-t');
            $dateRange = "$startDate - $endDate";
        }

        $totalEmployees = Account::find()->where(['id_company' => $id_company, 'status' => 1])->count();

        $presentInPeriod = \app\models\trx\Schedule::find()
            ->where(['id_company' => $id_company])
            ->andWhere(['between', 'date', $startDate, $endDate])
            ->andWhere(['not', ['checkin_datetime' => null]])
            ->count();

        $pendingApprovals = \app\models\trx\LeaveRequest::find()
            ->innerJoinWith('user')
            ->where(['user.id_company' => $id_company, 'leave_request.status' => \app\models\trx\LeaveRequest::STATUS_PENDING])
            ->count();

        $monthlyPayroll = \app\models\trx\Payroll::find()
            ->where(['id_company' => $id_company, 'status' => \app\models\trx\Payroll::STATUS_APPROVE])
            ->andWhere(['>=', 'period_start', $startDate])
            ->andWhere(['<=', 'period_end', $endDate])
            ->sum('net_salary') ?: 0;

        $recentAttendances = \app\models\trx\Schedule::find()
            ->with(['user'])
            ->where(['id_company' => $id_company])
            ->andWhere(['between', 'date', $startDate, $endDate])
            ->orderBy(['date' => SORT_DESC, 'checkin_datetime' => SORT_DESC])
            ->limit(10)
            ->all();

        return $this->render('index', [
            'totalEmployees' => $totalEmployees,
            'presentInPeriod' => $presentInPeriod,
            'pendingApprovals' => $pendingApprovals,
            'monthlyPayroll' => $monthlyPayroll,
            'recentAttendances' => $recentAttendances,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'dateRange' => $dateRange,
        ]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'blank';

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            $user = GeneralHelper::identity();
            GeneralHelper::session('id_client', $user->id_client);
            GeneralHelper::session('id_company', $user->id_company);
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionEnv()
    {
        $req = Yii::$app->request;
        if ($req->isPost) {
            $id_client = $req->post('id_client');
            $id_company = $req->post('id_company');

            $valRole = 1;
            if (RoleHelper::isSuper()) {
                $valRole = 4;
            } else if (RoleHelper::allCompany()) {
                $valRole = 3;
            } else if (RoleHelper::allUser()) {
                $valRole = 2;
            }

            if ($id_client && $valRole == 4) {
                GeneralHelper::session('id_client', $id_client);
                $listCompany = Company::getList($id_client);
                $id_company = array_key_first($listCompany);
                if (!$id_company) {
                    $user = $this->user;
                    GeneralHelper::session('id_client', $user->id_client);
                    $id_company = $user->id_company;
                }
            }

            if ($id_company && in_array($valRole, [4, 3])) {
                GeneralHelper::session('id_company', $id_company);
            }
        }

        return 1;
    }

    /**
     * Register action.
     *
     * @return Response|string
     */
    public function actionRegister()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'blank';

        $model = new RegisterForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->register()) {

                // Find client to get token
                $client = \app\models\master\Client::findOne(['email' => $model->email]);
                if ($client) {
                    $this->sendActivationEmail($client);
                }
                GeneralHelper::flashSucceed('Registrasi berhasil! Silakan cek email Anda untuk aktivasi akun perusahaan.');
                return $this->redirect(['login']);
            }
            GeneralHelper::flashFailed(Html::errorSummary($model));
        }

        return $this->render('register', [
            'model' => $model,
        ]);
    }

    public function actionForgotPassword()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $this->layout = 'blank';
        if (Yii::$app->request->isPost) {
            $email = Yii::$app->request->post('email');
            $user = Account::findOne(['email' => $email]);
            if ($user) {
                if ($user->client && $user->client->is_active == 1) {
                    // buat token dengan gabungan email dan datetime now
                    $user->token = md5($email . date('Y-m-d H:i:s'));
                    if ($user->save(false)) {
                        $this->sendPasswordResetEmail($user);
                        GeneralHelper::flashSucceed('Email reset password telah dikirim ke ' . \yii\bootstrap5\Html::encode($email));
                    } else {
                        GeneralHelper::flashFailed('Gagal memproses permintaan.');
                    }
                } else {
                    GeneralHelper::flashFailed('Akun perusahaan Anda belum aktif. Silakan hubungi admin atau cek email aktivasi.');
                }
            } else {
                GeneralHelper::flashFailed('Email tidak ditemukan di sistem.');
            }
            return $this->redirect(['forgot-password']);
        }
        return $this->render('forgot-password');
    }

    public function actionNewPassword($token = null)
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $this->layout = 'blank';
        if (!$token) {
            Yii::$app->session->setFlash('error', 'Token invalid');
            return $this->redirect(['login']);
        }

        $user = Account::findOne(['token' => $token]);
        if (!$user) {
            Yii::$app->session->setFlash('error', 'Token invalid');
            return $this->redirect(['login']);
        }

        if (Yii::$app->request->isPost) {
            $password = Yii::$app->request->post('password');
            $confirmPassword = Yii::$app->request->post('confirm_password');

            if ($password && $password === $confirmPassword) {
                $user->password = md5($password);
                $user->token = null;
                if ($user->save(false)) {
                    GeneralHelper::flashSucceed('Password berhasil diubah. Silakan login.');
                    return $this->redirect(['login']);
                } else {
                    GeneralHelper::flashFailed('Gagal menyimpan password baru.');
                }
            } else {
                Yii::$app->session->setFlash('error', 'Password tidak cocok atau kosong.');
            }
        }
        return $this->render('new-password', [
            'user' => $user,
        ]);
    }

    public function actionActivate($token)
    {
        $client = \app\models\master\Client::findOne(['token' => $token]);
        if (!$client) {
            GeneralHelper::flashFailed('Token aktivasi tidak valid atau sudah kadaluarsa.');
            return $this->redirect(['login']);
        }

        $client->is_active = 1;
        $client->token = null;
        $client->confirm_at = date('Y-m-d H:i:s');
        if ($client->save()) {
            GeneralHelper::flashSucceed('Akun perusahaan Anda berhasil diaktifkan. Silakan login.');
        } else {
            GeneralHelper::flashFailed('Gagal mengaktifkan akun perusahaan.');
        }
        return $this->redirect(['login']);
    }

    protected function sendActivationEmail($client)
    {
        $client->confirmation_sent_at = DBHelper::now();
        $client->save(false);

        return Yii::$app->mailer->compose(
            ['html' => 'activation-html'],
            ['client' => $client]
        )
            ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->name])
            ->setTo($client->email)
            ->setSubject('Aktivasi Akun Perusahaan - ' . Yii::$app->name)
            ->send();
    }

    protected function sendPasswordResetEmail($user)
    {
        return Yii::$app->mailer->compose(
            ['html' => 'passwordResetToken-html'],
            ['user' => $user]
        )
            ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->name])
            ->setTo($user->email)
            ->setSubject('Reset Password - ' . Yii::$app->name)
            ->send();
    }

    public function actionInit($id = null, $id_branch = null)
    {
        ini_set('max_execution_time', 60);
        switch ($id) {
            case "menu":
                DBHelper::initMenu();
                GeneralHelper::cacheFlush();
                die($id);
            case "view":
                DBHelper::initView();
                die($id);
            case "flush":
                GeneralHelper::cacheFlush();
                die($id);
            case "opcache":
                if (function_exists('opcache_get_status')) {
                    $status = opcache_get_status();
                    if ($status && $status['opcache_enabled']) {
                        echo "OPcache aktif<br>";
                        echo "Memory used: " . $status['memory_usage']['used_memory'] . " bytes";
                    } else {
                        echo "OPcache tidak aktif";
                    }
                } else {
                    echo "OPcache tidak tersedia di server ini.";
                }
                die;
            default:
                die('id salah');
        }
    }

}