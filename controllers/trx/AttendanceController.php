<?php

namespace app\controllers\trx;

use app\models\trx\search\ScheduleSearch;
use Yii;
use app\controllers\BaseController;
use app\helpers\RoleHelper;
use app\models\trx\Schedule;
use yii\web\NotFoundHttpException;

class AttendanceController extends BaseController
{

    public function actionIndex()
    {
        $searchModel = new ScheduleSearch();
        $params = Yii::$app->request->queryParams;
        $hideUser = false;
        if (!RoleHelper::allUser()) {
            $hideUser = true;
            $params['ScheduleSearch']['id_user'] = $this->user->id_user;
        }
        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'hideUser' => $hideUser,
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
