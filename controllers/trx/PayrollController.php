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
        $month = $month ?? date('m');
        $year = $year ?? date('Y');

        $periodStart = "$year-$month-01";
        $periodEnd = date('Y-m-t', strtotime($periodStart));

        $query = Payroll::find()->where(['id_company' => $this->id_company])
            ->andWhere(['period_start' => $periodStart, 'period_end' => $periodEnd]);

        $models = $query->all();

        // Get company allowance structure for display
        $company = Company::findOne($this->id_company);
        $companyAllowances = $company ? ($company->allowance ?? []) : [];

        return $this->render('index', [
            'models' => $models,
            'month' => $month,
            'year' => $year,
            'periodStart' => $periodStart,
            'periodEnd' => $periodEnd,
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

        $overtimeList = Schedule::find()
            ->andWhere(['id_company' => $this->id_company, 'is_overtime' => true])
            ->andWhere(['>', 'total_workhour', 0])
            ->andWhere(['>=', 'date', $periodStart])
            ->andWhere(['<=', 'date', $periodEnd])
            ->andWhere(['!=', 'checkin_datetime', null])
            ->andWhere(['!=', 'checkout_datetime', null])
            ->indexBy(['id_user'])
            ->all();

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
                    $payroll->hourly_rate = $user->hourly_rate?:0;

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

        // Handle allowance item updates
        if (strpos($field, 'allowance_item_') === 0) {
            $uuid = str_replace('allowance_item_', '', $field);
            $allowanceData = $model->allowance ?? [];
            if (is_string($allowanceData)) {
                $allowanceData = json_decode($allowanceData, true) ?? [];
            }
            $allowanceIndexed = ArrayHelper::index($allowanceData, 'uuid');

            if (isset($allowanceIndexed[$uuid])) {
                $allowanceIndexed[$uuid]['value'] = intval($value);
                $model->allowance = array_values($allowanceIndexed);

                // Recalculate total allowance and net salary
                $totalAllowance = 0;
                $allowanceForCalc = $model->allowance;
                if (is_string($allowanceForCalc)) {
                    $allowanceForCalc = json_decode($allowanceForCalc, true) ?? [];
                }
                if (is_array($allowanceForCalc)) {
                    foreach ($allowanceForCalc as $item) {
                        $totalAllowance += $item['value'] ?? 0;
                    }
                }
                $model->gross_salary = $model->basic_salary + $totalAllowance + $model->overtime;
                $model->net_salary = $model->gross_salary - $model->dedection - $model->tax;

                if ($model->save()) {
                    return [
                        'success' => true,
                        'gross_salary' => $model->gross_salary,
                        'gross_salary_formatted' => number_format($model->gross_salary, 0, ',', '.'),
                        'net_salary' => $model->net_salary,
                        'net_salary_formatted' => number_format($model->net_salary, 0, ',', '.'),
                        'total_allowance' => $totalAllowance,
                        'total_allowance_formatted' => number_format($totalAllowance, 0, ',', '.')
                    ];
                }
            }
        }

        if (in_array($field, ['basic_salary', 'overtime', 'dedection', 'tax'])) {
            $model->$field = intval($value);

            // Recalculate net salary with total allowance
            $totalAllowance = 0;
            $allowanceForCalc = $model->allowance;
            if (is_string($allowanceForCalc)) {
                $allowanceForCalc = json_decode($allowanceForCalc, true) ?? [];
            }
            if (is_array($allowanceForCalc)) {
                foreach ($allowanceForCalc as $item) {
                    $totalAllowance += $item['value'] ?? 0;
                }
            }
            $model->gross_salary = $model->basic_salary + $totalAllowance + $model->overtime;
            $model->net_salary = $model->gross_salary - $model->dedection - $model->tax;

            if ($model->save()) {
                return [
                    'success' => true,
                    'gross_salary' => $model->gross_salary,
                    'gross_salary_formatted' => number_format($model->gross_salary, 0, ',', '.'),
                    'net_salary' => $model->net_salary,
                    'net_salary_formatted' => number_format($model->net_salary, 0, ',', '.')
                ];
            }
        }

        return ['success' => false, 'message' => 'Failed to update.'];
    }

    public function actionVerify()
    {
        $id = $this->request->post('id');

        if ($id) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model = $this->findModel($id);

            if ($model->status === Payroll::STATUS_PENDING) {
                $model->status = Payroll::STATUS_DRAFT;
                $model->id_user_verify = $this->user->id_user;
                $model->user_verify_at = DBHelper::now();
                if ($model->save()) {
                    return ['success' => true];
                }
            }
        } else {
            return ['success' => false, 'message' => 'ID payroll tidak ditemukan.'];
        }

        return ['success' => false, 'message' => 'Verification failed.'];
    }

    public function actionApprove()
    {
        $id = $this->request->post('id');
        if ($id) {
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
        } else {
            return ['success' => false, 'message' => 'ID payroll tidak ditemukan.'];
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
