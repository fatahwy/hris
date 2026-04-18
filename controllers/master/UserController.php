<?php

namespace app\controllers\master;

use app\models\AuthAssignment;
use Yii;
use app\controllers\BaseController;
use app\helpers\GeneralHelper;
use app\models\master\Account;
use app\models\master\search\AccountSearch;
use yii\bootstrap5\Html;
use yii\web\NotFoundHttpException;

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
        return $this->render('view', [
            'model' => $this->findModel($id),
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
        $model = null;
        if ($id) {
            $model = $this->findModel($id);
        }

        if (!$model) {
            $model = new Account();
        }

        $modelAuthAssignment = $model->role ?? new AuthAssignment();
        $tempModelAuthAssignment = new AuthAssignment();

        $isNewRecord = $model->isNewRecord;
        if ($isNewRecord) {
            $model->scenario = 'create';
        }

        if ($model->load($this->request->post()) && $tempModelAuthAssignment->load($this->request->post())) {
            $model->id_client = $this->id_client;

            $transaction = Yii::$app->db->beginTransaction();
            try {
                // Handle password hashing if a new password is provided
                $postData = $this->request->post($model->formName());
                if (!empty($postData['password'])) {
                    $model->password = Yii::$app->getSecurity()->generatePasswordHash($postData['password']);
                } else if ($id) {
                    // If updating and password is not provided, keep the old password
                    $oldModel = $this->findModel($id);
                    $model->password = $oldModel->password;
                }

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
