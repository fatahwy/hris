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

    public function actionProcess($id_leave_type = null)
    {
        $model = null;
        if ($id_leave_type) {
            $model = $this->findModel($id_leave_type);
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

    public function actionDelete($id_leave_type)
    {
        $this->findModel($id_leave_type)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the LeaveType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id_leave_type Id LeaveType
     * @return LeaveType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id_leave_type)
    {
        if (($model = LeaveType::findOne(['id_leave_type' => $id_leave_type])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}