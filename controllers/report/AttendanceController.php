<?php

namespace app\controllers\report;

use app\controllers\BaseController;
use app\models\report\search\AttendanceSummarySearch;
use Yii;

class AttendanceController extends BaseController
{

    public function actionIndex()
    {
        $searchModel = new AttendanceSummarySearch();
        $searchModel->date_from = $searchModel->date_from ?: date('Y-m-01');
        $searchModel->date_to = $searchModel->date_to ?: date('Y-m-d');
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
