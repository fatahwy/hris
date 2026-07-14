<?php

namespace app\controllers;

use app\models\trx\Schedule;
use app\models\trx\search\PayrollSearch;
use app\models\trx\search\ScheduleSearch;
use Yii;
use yii\web\NotFoundHttpException;

class ProfileController extends BaseController
{

    public function actionIndex()
    {
        $model = $this->user;
        if (!$model) {
            throw new NotFoundHttpException('Halaman yang Anda cari tidak ditemukan.');
        }

        $type = $_GET['type'] ?? 'profile';
        $params = Yii::$app->request->queryParams;

        switch ($type) {
            case 'schedule':
                $scheduleSearch = new ScheduleSearch();
                $scheduleSearch->id_user = $model->id_user;
                $scheduleDataProvider = $scheduleSearch->search($params);

                $data = [
                    'hideUser' => true,
                    'searchModel' => $scheduleSearch,
                    'dataProvider' => $scheduleDataProvider,
                ];
                break;
            case 'payroll':
                $payrollSearch = new PayrollSearch();
                $payrollSearch->id_user = $model->id_user;
                $payrollDataProvider = $payrollSearch->search($params);

                $data = [
                    'payrollSearch' => $payrollSearch,
                    'payrollDataProvider' => $payrollDataProvider,
                ];
                break;
            default: // profile
                $company = $model->company;
                $companyAllowances = $company ? ($company->allowance ?? []) : [];

                $data = [
                    'model' => $model,
                    'companyAllowances' => $companyAllowances,
                ];
                break;
        }

        return $this->render('index', [
            'type' => $type,
            'model' => $model,
            'data' => $data,
        ]);
    }

    public function actionView($id)
    {
        if (($model = Schedule::findOne(['id_schedule' => $id])) !== null) {
            return $this->render('@app/views/trx/attendance/view', [
                'model' => $model,
            ]);
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
