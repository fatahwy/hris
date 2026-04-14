<?php

namespace app\controllers\master;

use app\controllers\BaseController;
use app\helpers\GeneralHelper;
use app\helpers\RoleHelper;
use app\models\master\Company;
use app\models\master\Department;
use app\models\master\search\DepartmentSearch;
use yii\bootstrap5\Html;
use yii\web\NotFoundHttpException;

/**
 * DepartmentController implements the CRUD actions for Department model.
 */
class DepartmentController extends BaseController
{

    /**
     * Lists all Department models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new DepartmentSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionProcess($id_department = null)
    {
        $model = null;
        if ($id_department) {
            $model = $this->findModel($id_department);
        }
        if (!$model) {
            $model = new Department();
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

    public function actionDelete($id_department)
    {
        $this->findModel($id_department)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Department model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id_department Id Department
     * @return Department the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id_department)
    {
        if (($model = Department::findOne(['id_department' => $id_department])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}