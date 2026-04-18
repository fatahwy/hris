<?php

namespace app\controllers\master;

use app\controllers\BaseController;
use app\helpers\GeneralHelper;
use app\helpers\RoleHelper;
use app\models\master\Company;
use app\models\master\LeaveType;
use app\models\master\search\LeaveTypeSearch;
use yii\bootstrap5\Html;
use yii\web\NotFoundHttpException;

/**
 * LeaveTypeController implements the CRUD actions for LeaveType model.
 */
class LeaveTypeController extends BaseController
{

    /**
     * Lists all LeaveType models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new LeaveTypeSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionProcess($id = null)
    {
        $model = null;
        if ($id) {
            $model = $this->findModel($id);
        }
        if (!$model) {
            $model = new LeaveType();
        }

        if ($model->load($this->request->post())) {
            $model->id_company = $this->id_company;

            if ($model->save()) {
                GeneralHelper::flashSucceed();
                return $this->redirect(['index']);
            }
            GeneralHelper::flashFailed(Html::errorSummary($model));
        }

        return $this->render('process', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the LeaveType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id Uuid
     * @return LeaveType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LeaveType::findOne(['uuid' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}