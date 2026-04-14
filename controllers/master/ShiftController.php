<?php

namespace app\controllers\master;

use app\controllers\BaseController;
use app\helpers\GeneralHelper;
use app\helpers\RoleHelper;
use app\models\master\Company;
use app\models\master\Shift;
use app\models\master\search\ShiftSearch;
use yii\bootstrap5\Html;
use yii\web\NotFoundHttpException;

/**
 * ShiftController implements the CRUD actions for Shift model.
 */
class ShiftController extends BaseController
{

    /**
     * Lists all Shift models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ShiftSearch();
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
            $model = new Shift();
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

    public function actionDelete($id_shift)
    {
        $this->findModel($id_shift)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Shift model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id_shift Id Shift
     * @return Shift the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id_shift)
    {
        if (($model = Shift::findOne(['id_shift' => $id_shift])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}