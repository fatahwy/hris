<?php

namespace app\controllers\trx;

use app\models\trx\search\ScheduleSearch;
use Yii;
use app\controllers\BaseController;
use app\models\trx\Schedule;
use yii\web\NotFoundHttpException;

class AttendanceController extends BaseController
{

    public function actionIndex()
    {
        $searchModel = new ScheduleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    protected function findModel($id)
    {
        if (($model = Schedule::findOne(['id_schedule' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
