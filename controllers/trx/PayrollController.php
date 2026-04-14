<?php

namespace app\controllers\trx;

use app\controllers\BaseController;
use app\helpers\DBHelper;
use app\helpers\RoleHelper;
use app\models\master\Account;
use app\models\trx\Payroll;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ForbiddenHttpException;

class PayrollController extends BaseController
{
    public function actionIndex($month = null, $year = null)
    {
        $month = $month ?? date('m');
        $year = $year ?? date('Y');

        $periodStart = "$year-$month-01";
        $periodEnd = date('Y-m-t', strtotime($periodStart));

        $query = Payroll::find()->where(['id_company' => $this->id_company])
            ->andWhere(['period_start' => $periodStart, 'period_end' => $periodEnd]);

        $models = $query->all();

        return $this->render('index', [
            'models' => $models,
            'month' => $month,
            'year' => $year,
            'periodStart' => $periodStart,
            'periodEnd' => $periodEnd,
        ]);
    }

    public function actionGenerate($month, $year)
    {
        $periodStart = "$year-$month-01";
        $periodEnd = date('Y-m-t', strtotime($periodStart));

        $users = Account::find()->where(['id_company' => $this->id_company, 'status' => 1])->all();

        foreach ($users as $user) {
            $exists = Payroll::find()->where([
                'id_user' => $user->id_user,
                'period_start' => $periodStart,
                'period_end' => $periodEnd
            ])->exists();

            if (!$exists) {
                $payroll = new Payroll();
                $payroll->id_company = $this->id_company;
                $payroll->id_user = $user->id_user;
                $payroll->period_start = $periodStart;
                $payroll->period_end = $periodEnd;
                $payroll->basic_salary = $user->basic_salary ?? 0;
                $payroll->allowance = 0;
                $payroll->overtime = 0;
                $payroll->dedection = 0;
                $payroll->tax = 0;
                $payroll->net_salary = $payroll->basic_salary;
                $payroll->status = Payroll::STATUS_PENDING;
                $payroll->id_user_generate = $this->user->id_user;

                if (!$payroll->save()) {
                    Yii::error("Failed to save payroll for user {$user->id_user}: " . json_encode($payroll->errors));
                }
            }
        }

        return $this->redirect(['index', 'month' => $month, 'year' => $year]);
    }

    public function actionUpdateCell()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = Yii::$app->request->post('id');
        $field = Yii::$app->request->post('field');
        $value = Yii::$app->request->post('value');

        $model = $this->findModel($id);

        if ($model->status !== 'PENDING') {
            return ['success' => false, 'message' => 'Cannot update verified payroll.'];
        }

        if (in_array($field, ['basic_salary', 'allowance', 'overtime', 'dedection', 'tax'])) {
            $model->$field = intval($value);
            $model->net_salary = $model->basic_salary + $model->allowance + $model->overtime - $model->dedection - $model->tax;

            if ($model->save()) {
                return [
                    'success' => true,
                    'net_salary' => $model->net_salary,
                    'net_salary_formatted' => number_format($model->net_salary, 0, ',', '.')
                ];
            }
        }

        return ['success' => false, 'message' => 'Failed to update.'];
    }

    public function actionVerify($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = $this->findModel($id);

        if ($model->status === 'PENDING') {
            $model->status = Payroll::STATUS_DRAFT;
            $model->id_user_verify = $this->user->id_user;
            $model->user_verify_at = DBHelper::now();
            if ($model->save()) {
                return ['success' => true];
            }
        }

        return ['success' => false, 'message' => 'Verification failed.'];
    }

    public function actionApprove($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!RoleHelper::approvalPayroll()) {
            throw new ForbiddenHttpException('You do not have permission to approve payroll.');
        }

        $model = $this->findModel($id);

        if ($model->status === Payroll::STATUS_DRAFT) {
            $model->status = Payroll::STATUS_APPROVE;
            $model->id_user_approve = $this->user->id_user;
            $model->user_approve_at = DBHelper::now();
            if ($model->save()) {
                return ['success' => true];
            }
        }

        return ['success' => false, 'message' => 'Approval failed.'];
    }

    protected function findModel($id)
    {
        if (($model = Payroll::findOne(['id_payroll' => $id, 'id_company' => $this->id_company])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
