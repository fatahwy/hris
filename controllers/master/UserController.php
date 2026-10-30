<?php

namespace app\controllers\master;

use app\models\AuthAssignment;
use Yii;
use app\controllers\BaseController;
use app\helpers\GeneralHelper;
use app\models\master\Account;
use app\models\master\Company;
use app\models\master\search\AccountSearch;
use yii\bootstrap5\Html;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * UserController implements the CRUD actions for Account model mapped to /master/user.
 */
class UserController extends BaseController
{
    /**
     * Lists all Account models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new AccountSearch();
        $searchModel->id_client = $this->id_client; // Filter by current client if applicable

        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Account model.
     * @param string $id Uuid
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $companyAllowances = $model->allowance ?? [];

        if ($companyAllowances) {
            foreach ($companyAllowances as $v) {
                $model->allowance_items[$v['uuid']] = $v['value'] ?? null;
            }
        }

        return $this->render('view', [
            'model' => $model,
            'companyAllowances' => $companyAllowances,
        ]);
    }

    /**
     * Creates or Updates Account model.
     *
     * @param string|null $id Uuid
     * @return string|\yii\web\Response
     */
    public function actionProcess($id = null)
    {
        // Get company allowance structure
        $company = Company::findOne($this->id_company);
        $companyAllowances = $company ? ($company->allowance ?? []) : [];

        // Validasi batas kuota max_user untuk create user baru
        if (!$id && $company && !$company->canAddUser()) {
            $maxUser = $company->getMaxUser();
            $paket = $company->getSubscriptionPaket();
            $paketText = $paket ? " ({$paket->nama_paket})" : "";
            GeneralHelper::flashFailed("Jumlah user telah mencapai batas maksimal paket subscription{$paketText} yaitu {$maxUser} user. Silakan upgrade paket langganan Anda untuk menambah user baru.");
            return $this->redirect(['index']);
        }

        $model = null;
        if ($id) {
            $model = $this->findModel($id);

            if ($model->allowance) {
                $userAllowance = ArrayHelper::index($model->allowance, 'uuid');
                foreach ($companyAllowances as $v) {
                    $model->allowance_items[$v['uuid']] = $userAllowance[$v['uuid']]['value'] ?? null;
                }
            }
        }

        if (!$model) {
            $model = new Account();
            $model->scenario = 'create';
            $model->id_company = $this->id_company;
            $model->annual_leave_quota = 12;
            $model->remaining_leave = 12;
            $model->bpjs_kesehatan = 1;
            $model->bpjs_jht = 1;
            $model->bpjs_jp = 1;
            $model->bpjs_jkk = 1;
            $model->bpjs_jkm = 1;
            $model->tax_method = 'Gross';
            $model->is_domicile_same_ktp = 1;
            $model->status = 1;
        }

        $modelAuthAssignment = $model->role ?? new AuthAssignment();
        $tempModelAuthAssignment = new AuthAssignment();

        $isNewRecord = $model->isNewRecord;
        if ($isNewRecord) {
            $model->scenario = 'create';
        }

        if ($model->load($this->request->post()) && $tempModelAuthAssignment->load($this->request->post())) {
            $model->id_client = $this->id_client;
            $model->id_company = !empty($model->id_company) ? $model->id_company : $this->id_company;
            if ($model->id_company && ($selectedCompany = Company::findOne($model->id_company))) {
                $model->work_location = $selectedCompany->name;
            }

            $targetCompany = Company::findOne($model->id_company);
            if ($isNewRecord && $targetCompany && !$targetCompany->canAddUser()) {
                $maxUser = $targetCompany->getMaxUser();
                $paket = $targetCompany->getSubscriptionPaket();
                $paketText = $paket ? " ({$paket->nama_paket})" : "";
                GeneralHelper::flashFailed("Jumlah user telah mencapai batas maksimal paket subscription{$paketText} yaitu {$maxUser} user. Silakan upgrade paket langganan Anda.");
                return $this->redirect(['index']);
            }

            $transaction = Yii::$app->db->beginTransaction();
            try {
                // Handle password hashing if a new password is provided
                $postData = $this->request->post($model->formName());
                if (!empty($postData['password'])) {
                    $model->password = md5($postData['password']);
                } else if ($id) {
                    // If updating and password is not provided, keep the old password
                    $oldModel = $this->findModel($id);
                    $model->password = $oldModel->password;
                }

                // Handle photo file upload
                $photoFile = UploadedFile::getInstance($model, 'photo_file');
                if ($photoFile) {
                    $ext = strtolower($photoFile->extension);
                    $allowedExts = ['png', 'jpg', 'jpeg', 'webp'];
                    if (!in_array($ext, $allowedExts)) {
                        GeneralHelper::flashFailed('Format foto profil tidak valid. Hanya png, jpg, jpeg, webp yang diizinkan.');
                        $transaction->rollBack();
                        return $this->redirect($id ? ['process', 'id' => $id] : ['process']);
                    }
                    if ($photoFile->size > 5 * 1024 * 1024) {
                        GeneralHelper::flashFailed('Ukuran foto profil maksimal 5MB.');
                        $transaction->rollBack();
                        return $this->redirect($id ? ['process', 'id' => $id] : ['process']);
                    }
                    $uploadDir = Yii::getAlias('@webroot/uploads/user');
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    $fileName = 'user_' . time() . '_' . uniqid() . '.' . $ext;
                    $filePath = $uploadDir . '/' . $fileName;
                    if ($photoFile->saveAs($filePath)) {
                        $model->photo = 'uploads/user/' . $fileName;
                    }
                } elseif ($id && empty($model->photo)) {
                    $oldModel = $this->findModel($id);
                    $model->photo = $oldModel->photo;
                }

                // Handle allowance data
                $allowanceData = [];
                $fixedAllowanceTotal = 0;

                if (isset($postData['allowance_items']) && is_array($postData['allowance_items'])) {
                    foreach ($companyAllowances as $companyAllowance) {
                        $uuid = $companyAllowance['uuid'];
                        $value = isset($postData['allowance_items'][$uuid]) ? (int) $postData['allowance_items'][$uuid] : 0;
                        $allowanceData[] = [
                            'uuid' => $uuid,
                            'name' => $companyAllowance['name'],
                            'is_fixed' => $companyAllowance['is_fixed'],
                            'value' => $value,
                        ];

                        // Sum fixed allowances for hourly rate calculation
                        if ($companyAllowance['is_fixed'] == '1') {
                            $fixedAllowanceTotal += $value;
                        }
                    }
                }
                $model->allowance = $allowanceData;

                // Calculate hourly rate: (basic_salary + fixed_allowances) / DIV_HOURLY_RATE
                $model->hourly_rate = (int) (((int) $model->basic_salary + $fixedAllowanceTotal) / GeneralHelper::DIV_HOURLY_RATE);

                if ($model->save()) {
                    if (!$isNewRecord) {
                        AuthAssignment::deleteAll(['user_id' => $model->id_user]);
                    }
                    $tempModelAuthAssignment->user_id = $model->id_user;
                    $tempModelAuthAssignment->created_at = time();
                    $tempModelAuthAssignment->save();

                    $transaction->commit();
                    GeneralHelper::flashSucceed();
                    return $this->redirect(['index']);
                }
                GeneralHelper::flashFailed(Html::errorSummary($model));
            } catch (\Throwable $th) {
                $transaction->rollBack();
                throw $th;
            }
        }
        $model->password = ''; // Clear password field on update for security/ux

        return $this->render('process', [
            'model' => $model,
            'modelAuthAssignment' => $modelAuthAssignment,
            'companyAllowances' => $companyAllowances,
        ]);
    }

    /**
     * Deletes an existing Account model.
     * @param string $id Uuid
     * @return \yii\web\Response
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        GeneralHelper::flashSucceed('Berhasil dihapus.');
        return $this->redirect(['index']);
    }

    /**
     * Finds the Account model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id Uuid
     * @return Account the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $model = Account::find()
            ->where(['uuid' => $id])
            ->andWhere(['id_client' => $this->id_client])
            ->one();

        if ($model) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
