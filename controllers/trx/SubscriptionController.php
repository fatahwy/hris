<?php

namespace app\controllers\trx;

use app\controllers\BaseController;
use app\helpers\DBHelper;
use app\helpers\GeneralHelper;
use app\helpers\RoleHelper;
use app\models\master\Company;
use app\models\master\Paket;
use app\models\trx\Subscription;
use app\models\trx\search\SubscriptionSearch;
use Yii;
use yii\bootstrap5\Html;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * SubscriptionController implements subscription renewal, history, approval, and cancellation.
 */
class SubscriptionController extends BaseController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'delete' => ['post'],
                'logout' => ['post'],
                'approve' => ['post'],
                'cancel' => ['post'],
            ],
        ];

        return $behaviors;
    }

    /**
     * Lists subscription history and current company subscription status.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SubscriptionSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        $company = $this->id_company ? Company::findOne($this->id_company) : null;
        $companyList = RoleHelper::isSuper()
            ? ArrayHelper::map(Company::find()->orderBy(['name' => SORT_ASC])->all(), 'id_company', 'name')
            : [];
        $paketList = ArrayHelper::map(
            Paket::find()->where(['status' => 1])->orderBy(['id_paket' => SORT_ASC])->all(),
            'id_paket',
            'nama_paket'
        );

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'company' => $company,
            'companyList' => $companyList,
            'paketList' => $paketList,
        ]);
    }

    /**
     * Form to request a subscription extension (1 month, 2 months, or any number of months).
     *
     * @return string|\yii\web\Response
     */
    public function actionProcess()
    {
        $model = new Subscription();
        $model->jumlah_bulan = 1;

        $company = $this->id_company ? Company::findOne($this->id_company) : null;
        $currentPaket = null;
        if ($company) {
            $activeSub = Subscription::find()
                ->where(['id_company' => $company->id_company, 'status' => Subscription::STATUS_ACTIVE])
                ->orderBy(['id_subscription' => SORT_DESC])
                ->one();
            $currentPaket = $activeSub ? $activeSub->paket : null;
        }

        $paketQuery = Paket::find()
            ->where(['status' => 1, 'durasi_satuan' => 'bulan'])
            ->orderBy(['id_paket' => SORT_ASC]);
        if ($currentPaket) {
            $paketQuery->andWhere(['>=', 'max_user', (int) $currentPaket->max_user]);
        }
        $pakets = $paketQuery->all();

        $companyList = RoleHelper::isSuper()
            ? ArrayHelper::map(Company::find()->orderBy(['name' => SORT_ASC])->all(), 'id_company', 'name')
            : [];

        if ($company) {
            $model->id_company = $company->id_company;
            if ($currentPaket) {
                foreach ($pakets as $p) {
                    if (strcasecmp($p->tier, $currentPaket->tier) === 0) {
                        $model->id_paket = $p->id_paket;
                        break;
                    }
                }
            }
        }
        if (empty($model->id_paket) && !empty($pakets)) {
            $model->id_paket = $pakets[0]->id_paket;
        }

        if ($model->load($this->request->post())) {
            $targetCompanyId = RoleHelper::isSuper() && !empty($model->id_company)
                ? (int) $model->id_company
                : (int) $this->id_company;

            $targetCompany = Company::findOne($targetCompanyId);
            if (!$targetCompany) {
                GeneralHelper::flashFailed('Perusahaan tidak ditemukan.');
                return $this->render('process', [
                    'model' => $model,
                    'company' => $company,
                    'pakets' => $pakets,
                    'currentPaket' => $currentPaket,
                    'companyList' => $companyList,
                ]);
            }

            $targetActiveSub = Subscription::find()
                ->where(['id_company' => $targetCompany->id_company, 'status' => Subscription::STATUS_ACTIVE])
                ->orderBy(['id_subscription' => SORT_DESC])
                ->one();
            $targetCurrentPaket = $targetActiveSub ? $targetActiveSub->paket : null;

            $paket = Paket::findOne([
                'id_paket' => (int) $model->id_paket,
                'status' => 1,
                'durasi_satuan' => 'bulan',
            ]);
            if (!$paket) {
                GeneralHelper::flashFailed('Paket langganan tidak valid.');
                return $this->render('process', [
                    'model' => $model,
                    'company' => $targetCompany,
                    'pakets' => $pakets,
                    'currentPaket' => $targetCurrentPaket,
                    'companyList' => $companyList,
                ]);
            }

            if ($targetCurrentPaket && (int) $paket->max_user < (int) $targetCurrentPaket->max_user) {
                $model->addError('id_paket', 'Tidak dapat turun paket dari paket saat ini (' . $targetCurrentPaket->nama_paket . ').');
                GeneralHelper::flashFailed(Html::errorSummary($model));
                return $this->render('process', [
                    'model' => $model,
                    'company' => $targetCompany,
                    'pakets' => $pakets,
                    'currentPaket' => $targetCurrentPaket,
                    'companyList' => $companyList,
                ]);
            }

            $months = (int) $model->jumlah_bulan;
            if ($months < 1) {
                $model->addError('jumlah_bulan', 'Jumlah bulan minimal 1 bulan.');
                GeneralHelper::flashFailed(Html::errorSummary($model));
                return $this->render('process', [
                    'model' => $model,
                    'company' => $targetCompany,
                    'pakets' => $pakets,
                    'currentPaket' => $targetCurrentPaket,
                    'companyList' => $companyList,
                ]);
            }

            $now = DBHelper::now();
            $model->id_company = $targetCompany->id_company;
            $model->id_client = $targetCompany->id_client;
            $model->id_paket = $paket->id_paket;
            $model->jumlah_bulan = $months;
            $model->durasi = $months * (int) $paket->durasi_hari;
            $model->price = (float) $paket->price * $months;
            $model->status = Subscription::STATUS_PENDING;
            $model->created_at = $now;
            $model->updated_at = $now;

            if ($model->save()) {
                GeneralHelper::flashSucceed('Pengajuan perpanjangan subscription berhasil dibuat. Silakan lakukan transfer pembayaran.');
                return $this->redirect(['view', 'id' => $model->uuid]);
            }

            GeneralHelper::flashFailed(Html::errorSummary($model));
        }

        return $this->render('process', [
            'model' => $model,
            'company' => $company,
            'pakets' => $pakets,
            'currentPaket' => $currentPaket,
            'companyList' => $companyList,
        ]);
    }

    /**
     * Displays a single Subscription model.
     * Shows bank transfer instructions if status is still pending approval.
     *
     * @param string $id Uuid
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Approve a pending subscription (Super role only).
     *
     * @param string $id Uuid
     * @return \yii\web\Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionApprove($id)
    {
        if (!RoleHelper::isSuper()) {
            throw new ForbiddenHttpException('Anda tidak memiliki akses untuk menyetujui subscription.');
        }

        $model = $this->findModel($id);
        if (!$model->isPending()) {
            GeneralHelper::flashFailed('Pengajuan subscription ini sudah diproses sebelumnya.');
            return $this->redirect(['view', 'id' => $model->uuid]);
        }

        if ($model->approve((int) $this->user->id_user)) {
            $company = $model->company;
            $expiredFormatted = $company && $company->expired_at
                ? Yii::$app->formatter->asDatetime($company->expired_at)
                : '-';
            GeneralHelper::flashSucceed("Subscription berhasil disetujui. Masa aktif perusahaan diperpanjang hingga {$expiredFormatted}.");
        } else {
            GeneralHelper::flashFailed('Gagal menyetujui subscription.');
        }

        return $this->redirect(['view', 'id' => $model->uuid]);
    }

    /**
     * Cancel a pending subscription (by Super or user of that company).
     *
     * @param string $id Uuid
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionCancel($id)
    {
        $model = $this->findModel($id);
        if (!$model->isPending()) {
            GeneralHelper::flashFailed('Hanya pengajuan yang masih menunggu pembayaran yang dapat dibatalkan.');
            return $this->redirect(['view', 'id' => $model->uuid]);
        }

        if ($model->cancel((int) $this->user->id_user)) {
            GeneralHelper::flashSucceed('Pengajuan subscription berhasil dibatalkan.');
        } else {
            GeneralHelper::flashFailed('Gagal membatalkan pengajuan subscription.');
        }

        return $this->redirect(['view', 'id' => $model->uuid]);
    }

    /**
     * Finds the Subscription model based on its uuid.
     * Non-super users are restricted to their own company.
     *
     * @param string $id Uuid
     * @return Subscription
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        $query = Subscription::find()->where(['uuid' => $id]);
        if (!RoleHelper::isSuper()) {
            $query->andWhere(['id_company' => $this->id_company]);
        }

        $model = $query->one();
        if ($model) {
            return $model;
        }

        throw new NotFoundHttpException('Data subscription tidak ditemukan.');
    }
}
