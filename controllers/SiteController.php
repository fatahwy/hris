<?php

namespace app\controllers;

use app\helpers\DBHelper;
use app\helpers\GeneralHelper;
use app\helpers\RoleHelper;
use app\models\master\Company;
use app\models\master\Paket;
use Yii;
use yii\bootstrap5\Html;
use yii\web\Response;
use app\models\LoginForm;
use app\models\RegisterForm;
use app\models\master\Account;
use app\models\trx\LeaveRequest;
use app\models\trx\Payroll;
use app\models\trx\Schedule;

class SiteController extends BaseController
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        array_unshift($behaviors['access']['rules'], [
            'actions' => ['index', 'check-session'],
            'allow' => true,
            'roles' => ['?', '@'],
        ]);
        return $behaviors;
    }

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
        $this->layout = false;

        $pakets = Paket::find()->where(['status' => 1])->orderBy(['max_user' => SORT_ASC, 'durasi_hari' => SORT_ASC])->all();

        return $this->render('index', [
            'pakets' => $pakets,
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
            return $this->redirect(['/dashboard']);
        }

        $this->layout = 'blank';

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            $user = GeneralHelper::identity();
            GeneralHelper::session('id_client', $user->id_client);
            GeneralHelper::session('id_company', $user->id_company);
            return $this->goBack(['/dashboard']);
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

        return $this->redirect(['/login']);
    }

    /**
     * Check session and company expiration status
     *
     * @return Response
     */
    public function actionCheckSession()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'guest',
                'redirect' => \yii\helpers\Url::to(['/site/login']),
            ];
        }

        $user = Yii::$app->user->identity;
        $company = $user->company ?? ($user->id_company ? Company::findOne($user->id_company) : null);

        if ($company && $company->isExpired()) {
            Yii::$app->user->logout();
            Yii::$app->session->setFlash('error', 'Akun perusahaan Anda telah berakhir. Silakan hubungi admin untuk perpanjangan.');
            return [
                'status' => 'expired',
                'redirect' => \yii\helpers\Url::to(['/site/login']),
            ];
        }

        return ['status' => 'ok'];
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
            return $this->redirect(['/dashboard']);
        }

        $this->layout = 'blank';

        $model = new RegisterForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->register()) {

                // Find client and paket
                $client = $model->client ?: \app\models\master\Client::findOne(['email' => $model->email]);
                $paket = $model->paket ?: \app\models\master\Paket::findOne($model->id_paket);

                if ($client) {
                    $isTrial = $paket && strtoupper($paket->kode) === 'TRIAL';
                    if ($isTrial) {
                        $this->sendActivationEmail($client);
                        GeneralHelper::flashSucceed('Registrasi berhasil! Silakan cek email Anda untuk aktivasi akun perusahaan.');
                    } else {
                        $this->sendPaymentEmail($client, $paket);
                        GeneralHelper::flashSucceed('Registrasi berhasil! Silakan cek email Anda untuk instruksi pembayaran paket ' . ($paket ? Html::encode($paket->nama_paket) : '') . ' dan konfirmasi bukti bayar ke admin.');
                    }
                } else {
                    GeneralHelper::flashSucceed('Registrasi berhasil! Silakan login.');
                }
                return $this->redirect(['login']);
            }
            GeneralHelper::flashFailed(Html::errorSummary($model));
        } else {
            $idPaketParam = Yii::$app->request->get('id_paket');
            if ($idPaketParam && \app\models\master\Paket::find()->where(['id_paket' => $idPaketParam, 'status' => 1])->exists()) {
                $model->id_paket = (int)$idPaketParam;
            } else {
                $trial = \app\models\master\Paket::findOne(['kode' => 'TRIAL']);
                if ($trial) {
                    $model->id_paket = $trial->id_paket;
                }
            }
            $refParam = Yii::$app->request->get('referensi') ?: Yii::$app->request->get('ref');
            if ($refParam) {
                $model->referensi = (string)$refParam;
            }
        }

        return $this->render('register', [
            'model' => $model,
        ]);
    }

    public function actionForgotPassword()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['/dashboard']);
        }
        $this->layout = 'blank';
        if (Yii::$app->request->isPost) {
            $email = Yii::$app->request->post('email');
            $user = Account::findOne(['email' => $email]);
            if ($user) {
                if ($user->client && $user->client->is_active == 1) {
                    $user->token = Yii::$app->security->generateRandomString(32);
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
            return $this->redirect(['/dashboard']);
        }
        $this->layout = 'blank';
        if (empty($token) || !is_string($token) || strlen(trim($token)) < 16) {
            Yii::$app->session->setFlash('error', 'Token invalid');
            return $this->redirect(['login']);
        }

        $user = Account::findOne(['token' => trim($token)]);
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

        Company::updateAll(['status' => 1], ['id_client' => $client->id_client ?: $client->id]);
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

    protected function sendPaymentEmail($client, $paket)
    {
        $client->confirmation_sent_at = DBHelper::now();
        $client->save(false);

        $adminPhone = GeneralHelper::ADMIN_NO_PHONE;

        return Yii::$app->mailer->compose(
            ['html' => 'payment-html'],
            [
                'client' => $client,
                'paket' => $paket,
                'adminPhone' => $adminPhone,
                'bankName' => GeneralHelper::BANK_NAME,
                'bankAccountNumber' => GeneralHelper::BANK_ACCOUNT_NUMBER,
                'bankAccountName' => GeneralHelper::BANK_ACCOUNT_NAME,
            ]
        )
            ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->name])
            ->setTo($client->email)
            ->setSubject('Instruksi Pembayaran Paket ' . ($paket ? $paket->nama_paket : '') . ' - ' . Yii::$app->name)
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
        if (!RoleHelper::isSuper()) {
            throw new \yii\web\ForbiddenHttpException('Akses ditolak.');
        }

        ini_set('max_execution_time', 60);
        switch ($id) {
            case "menu":
                DBHelper::initMenu();
                die('Menu reinitialized.');
            case "view":
                DBHelper::initView();
                die('Views reinitialized.');
            case "flush":
                GeneralHelper::cacheFlush();
                $auth = Yii::$app->authManager;
                if ($auth instanceof \yii\rbac\DbManager) {
                    $auth->invalidateCache();
                    die('Cache cleared.');
                }
                die('Cache flushed.');
            case "opcache":
                if (function_exists('opcache_get_status')) {
                    $status = opcache_get_status();
                    if ($status && $status['opcache_enabled']) {
                        echo "OPcache aktif<br>";
                        echo "Memory used: " . (int)$status['memory_usage']['used_memory'] . " bytes";
                    } else {
                        echo "OPcache tidak aktif";
                    }
                } else {
                    echo "OPcache tidak tersedia di server ini.";
                }
                die;
            default:
                die('Parameter id tidak valid.');
        }
    }
}
