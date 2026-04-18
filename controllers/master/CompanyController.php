<?php

namespace app\controllers\master;

use app\controllers\BaseController;
use app\helpers\GeneralHelper;
use app\helpers\RoleHelper;
use app\models\master\Company;
use app\models\master\search\CompanySearch;
use yii\bootstrap5\Html;
use yii\web\NotFoundHttpException;

/**
 * CompanyController implements the CRUD actions for Company model.
 */
class CompanyController extends BaseController
{

    /**
     * Lists all Company models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new CompanySearch();
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
            $model = new Company();
        }

        if ($model->load($this->request->post())) {
            $model->id_client = $this->id_client;

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
     * Finds the Company model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id Uuid
     * @return Company the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $model = Company::find()
            ->where(['uuid' => $id])
            ->andWhere(['id_client' => $this->id_client])
            ->one();

        if ($model) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}