<?php

namespace app\controllers\master;

use app\controllers\BaseController;
use app\helpers\GeneralHelper;
use app\models\master\Client;
use app\models\master\search\ClientSearch;
use yii\bootstrap5\Html;
use yii\web\NotFoundHttpException;

/**
 * ClientController implements the CRUD actions for Client model.
 */
class ClientController extends BaseController
{

    /**
     * Lists all Client models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ClientSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Updates an existing Client model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id Uuid
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionProcess($id = null)
    {
        $model = null;
        if ($id) {
            $model = $this->findModel($id);
        }
        if (!$model) {
            $model = new Client();
        }

        if ($model->load($this->request->post())) {
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
     * Deletes an existing Client model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id Uuid
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Client model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id Uuid
     * @return Client the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Client::findOne(['uuid' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}