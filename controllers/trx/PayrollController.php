<?php

namespace app\controllers\trx;

use app\controllers\BaseController;
use app\helpers\DBHelper;
use app\helpers\GeneralHelper;
use app\helpers\RoleHelper;
use app\models\master\Account;
use app\models\master\Company;
use app\models\trx\Payroll;
use app\models\trx\Schedule;
use Yii;
use yii\bootstrap5\Html;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ForbiddenHttpException;

class PayrollController extends BaseController
{
    public function actionIndex($month = null, $year = null)
    {
        $month = $month ?: date('n');
        $year = $year ?: date('Y');

        $periodStart = date('Y-m-d', strtotime("$year-$month-01"));
        $periodEnd = date('Y-m-t', strtotime($periodStart));

        $models = Payroll::find()->where(['id_company' => $this->id_company])
            ->andWhere(['period_start' => $periodStart, 'period_end' => $periodEnd])
            ->all();

        // Get company allowance structure for display
        $company = Company::findOne($this->id_company);
        $companyAllowances = $company ? ($company->allowance ?? []) : [];

        return $this->render('index', [
            'models' => $models,
            'month' => $month,
            'year' => $year,
            'companyAllowances' => $companyAllowances,
        ]);
    }

    public function actionGenerate($month, $year)
    {
        $periodStart = "$year-$month-01";
        $periodEnd = date('Y-m-t', strtotime($periodStart));

        $users = Account::find()->where(['id_company' => $this->id_company, 'status' => 1])->all();
        $company = Company::findOne($this->id_company);
        $companyAllowances = $company ? ($company->allowance ?? []) : [];

        // $cutoffSalary = 28;
        // $startDate = date('Y-m-' . $cutoffSalary, strtotime('-1 month'));
        // $endDate = date('Y-m-' . $cutoffSalary, strtotime($periodEnd));

        $modelOvertime = Schedule::find()
            ->andWhere(['id_company' => $this->id_company, 'is_overtime' => true])
            ->andWhere(['>', 'total_workhour', 0])
            ->andWhere(['>=', 'date', $periodStart])
            ->andWhere(['<=', 'date', $periodEnd])
            ->andWhere(['!=', 'checkin_datetime', null])
            ->andWhere(['!=', 'checkout_datetime', null])
            ->all();

        $overtimeList = [];
        foreach ($modelOvertime as $m) {
            $overtimeList[$m->id_user][] = $m;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $flag = true;
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
                    $payroll->ptkp = $user->ptkp;
                    $payroll->hourly_rate = $user->hourly_rate ?: 0;

                    // Calculate total allowance from user's allowance data
                    $allowanceData = [];
                    $totalAllowance = 0;
                    if (!empty($user->allowance) && is_array($user->allowance)) {
                        $userAllowance = ArrayHelper::index($user->allowance, 'uuid');
                        foreach ($companyAllowances as $companyAllowance) {
                            $uuid = $companyAllowance['uuid'];
                            $value = $userAllowance[$uuid]['value'] ?? 0;
                            $allowanceData[] = [
                                'uuid' => $uuid,
                                'name' => $companyAllowance['name'],
                                'is_fixed' => $companyAllowance['is_fixed'],
                                'value' => $value,
                            ];
                            $totalAllowance += $value;
                        }
                    }
                    $payroll->allowance = $allowanceData;

                    $payroll->overtime = $payroll->calculateOvertimePay($overtimeList[$user->id_user] ?? []);
                    $payroll->dedection = 0;

                    $payroll->gross_salary = $payroll->basic_salary + $totalAllowance;
                    $payroll->ter = Payroll::getTER(Account::listPtkp()[$payroll->ptkp] ?? null, $payroll->gross_salary);
                    $payroll->tax = $payroll->gross_salary * $payroll->ter;

                    $payroll->net_salary = $payroll->gross_salary - $payroll->tax;
                    $payroll->status = Payroll::STATUS_PENDING;
                    $payroll->id_user_generate = $this->user->id_user;
                    if (!$payroll->save()) {
                        $flag = $flag && false;
                        GeneralHelper::flashFailed(Html::errorSummary($payroll));
                    }
                }
            }

            if ($flag) {
                GeneralHelper::flashSucceed('Proses generate berhasil.');
                $transaction->commit();
            }
        } catch (\Throwable $th) {
            throw $th;
        }

        return $this->redirect(['index', 'month' => $month, 'year' => $year]);
    }

    public function actionVerify($id)
    {
        if ($this->request->isPost) {
            $model = $this->findModel($id);

            if ($model->status === Payroll::STATUS_PENDING) {
                $model->status = Payroll::STATUS_DRAFT;
                $model->id_user_verify = $this->user->id_user;
                $model->user_verify_at = DBHelper::now();
                if ($model->save()) {
                    $strtotime = strtotime($model->period_start);
                    GeneralHelper::flashSucceed('Payroll telah diverifikasi');
                    return $this->redirect(['index', 'month' => date('n', $strtotime), 'year' => date('Y', $strtotime)]);
                }
            }
        }

        return $this->redirect(['index']);
    }

    public function actionApprove($id)
    {
        if ($this->request->isPost) {
            if (!RoleHelper::approvalPayroll()) {
                throw new ForbiddenHttpException('You do not have permission to approve payroll.');
            }

            $model = $this->findModel($id);

            if ($model->status === Payroll::STATUS_DRAFT) {
                $model->status = Payroll::STATUS_APPROVE;
                $model->id_user_approve = $this->user->id_user;
                $model->user_approve_at = DBHelper::now();
                if ($model->save()) {
                    $strtotime = strtotime($model->period_start);
                    GeneralHelper::flashSucceed('Payroll telah disetujui');
                    return $this->redirect(['index', 'month' => date('n', $strtotime), 'year' => date('Y', $strtotime)]);
                }
            }
        }

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Payroll::findOne(['id_payroll' => $id, 'id_company' => $this->id_company])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
