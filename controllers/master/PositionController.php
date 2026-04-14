<?php

namespace app\controllers\master;

use app\controllers\BaseController;
use app\helpers\GeneralHelper;
use app\helpers\RoleHelper;
use app\models\master\Company;
use app\models\master\Position;
use app\models\master\search\PositionSearch;
use yii\bootstrap5\Html;
use yii\web\NotFoundHttpException;

/**
 * PositionController implements the CRUD actions for Position model.
 */
class PositionController extends BaseController
{

    /**
     * Lists all Position models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new PositionSearch();
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
            $model = new Position();
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
     * Finds the Position model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id_department Id Position
     * @return Position the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id_department)
    {
        if (($model = Position::findOne(['id_department' => $id_department])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}