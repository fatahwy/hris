<?php

namespace app\controllers;

use app\helpers\GeneralHelper;
use app\helpers\RoleHelper;
use app\models\trx\Payroll;
use app\models\trx\Schedule;
use app\models\trx\search\PayrollSearch;
use app\models\trx\search\ScheduleSearch;
use kartik\mpdf\Pdf;
use Yii;
use yii\web\ForbiddenHttpException;
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

    public function actionViewPayroll($id)
    {
        if (!GeneralHelper::isUuid($id)) {
            throw new NotFoundHttpException('Data payroll tidak ditemukan.');
        }

        $payroll = Payroll::find()
            ->with('user')
            ->where(['uuid' => $id]);

        if (!RoleHelper::allUser()) {
            $payroll->andWhere(['id_user' => $this->user->id_user]);
        }

        $payroll = $payroll->one();

        if (!$payroll) {
            throw new NotFoundHttpException('Data payroll tidak ditemukan.');
        }

        // Pastikan hanya payroll milik user yang login yang dapat diakses (kecuali admin/company)
        if ($payroll->id_user !== $this->user->id_user && $payroll->id_company !== $this->id_company) {
            throw new ForbiddenHttpException('Anda tidak memiliki akses ke slip gaji ini.');
        }

        $format = Yii::$app->request->get('format', 'html');

        if (true) {
            $content = $this->renderPartial('@app/views/trx/payroll/slip', [
                'model' => $payroll,
                'isPdf' => true,
            ]);

            $pdf = new Pdf([
                'mode' => Pdf::MODE_UTF8,
                'format' => Pdf::FORMAT_A4,
                'orientation' => Pdf::ORIENT_PORTRAIT,
                'destination' => Pdf::DEST_BROWSER,
                'content' => $content,
                'cssInline' => 'body { font-family: Helvetica, Arial, sans-serif; }',
                'options' => [
                    'title' => 'Slip Gaji - ' . ($payroll->user ? $payroll->user->name : ''),
                ],
                'methods' => [
                    'SetHeader' => ['Slip Gaji||Dicetak pada: ' . date('d/m/Y H:i')],
                    'SetFooter' => ['|Halaman {PAGENO}|'],
                ]
            ]);

            return $pdf->render();
        }

        return $this->renderPartial('@app/views/trx/payroll/slip', [
            'model' => $payroll,
            'isPdf' => false,
        ]);
    }

}
