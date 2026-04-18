<?php

namespace app\controllers\master;

use app\controllers\BaseController;
use app\helpers\GeneralHelper;
use app\helpers\RoleHelper;
use app\models\AuthItem;
use app\models\master\search\RoleSearch;
use yii\bootstrap5\Html;
use yii\web\NotFoundHttpException;

/**
 * RoleController implements the CRUD actions for AuthItem model.
 */
class RoleController extends BaseController
{

    /**
     * Lists all AuthItem models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new RoleSearch();
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
            $model = new AuthItem();
        }

        if ($model->load($this->request->post())) {
            $id_client = $this->id_client;

            $model->name = $model->label . $id_client;
            $model->type = 1;
            if (!in_array(strtolower(trim($model->name)), ['super', 'owner'])) {
                $model->id_client = $id_client;
            }

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

    /**
     * Deletes an existing AuthItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id_client Id AuthItem
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();
        GeneralHelper::flashSucceed();

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        $model = AuthItem::getQuery()
            ->andWhere(['name' => $id])
            ->one();

        if ($model) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}