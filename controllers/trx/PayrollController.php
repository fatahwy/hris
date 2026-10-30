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
use kartik\mpdf\Pdf;
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

        $companySettings = $company ? $company->getSetting() : [];
        $deductionSettings = $companySettings['deductions'] ?? [];

        $modelSchedules = Schedule::find()
            ->andWhere(['id_company' => $this->id_company])
            ->andWhere(['>=', 'date', $periodStart])
            ->andWhere(['<=', 'date', $periodEnd])
            ->all();

        $scheduleList = [];
        foreach ($modelSchedules as $m) {
            $scheduleList[$m->id_user][] = $m;
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
                    $this->populatePayrollData($payroll, $user, $periodStart, $periodEnd, $companyAllowances, $overtimeList, $scheduleList, $deductionSettings);
                    if (!$payroll->save()) {
                        $flag = false;
                        GeneralHelper::flashFailed(Html::errorSummary($payroll));
                        break;
                    }
                }
            }

            if ($flag) {
                GeneralHelper::flashSucceed('Proses generate berhasil.');
                $transaction->commit();
            } else {
                $transaction->rollBack();
            }
        } catch (\Throwable $th) {
            $transaction->rollBack();
            throw $th;
        }

        return $this->redirect(['index', 'month' => $month, 'year' => $year]);
    }

    public function actionRegenerate($month, $year)
    {
        $periodStart = "$year-$month-01";
        $periodEnd = date('Y-m-t', strtotime($periodStart));

        $users = Account::find()->where(['id_company' => $this->id_company, 'status' => 1])->all();
        $company = Company::findOne($this->id_company);
        $companyAllowances = $company ? ($company->allowance ?? []) : [];

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

        $companySettings = $company ? $company->getSetting() : [];
        $deductionSettings = $companySettings['deductions'] ?? [];

        $modelSchedules = Schedule::find()
            ->andWhere(['id_company' => $this->id_company])
            ->andWhere(['>=', 'date', $periodStart])
            ->andWhere(['<=', 'date', $periodEnd])
            ->all();

        $scheduleList = [];
        foreach ($modelSchedules as $m) {
            $scheduleList[$m->id_user][] = $m;
        }

        $existingPayrolls = Payroll::find()->where([
            'id_company' => $this->id_company,
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
        ])->indexBy('id_user')->all();

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $flag = true;
            $resetCount = 0;
            $addedCount = 0;
            $skippedCount = 0;

            foreach ($users as $user) {
                if (isset($existingPayrolls[$user->id_user])) {
                    $payroll = $existingPayrolls[$user->id_user];
                    // Reset payroll jika status masih PENDING
                    if ($payroll->status === Payroll::STATUS_PENDING) {
                        $this->populatePayrollData($payroll, $user, $periodStart, $periodEnd, $companyAllowances, $overtimeList, $scheduleList, $deductionSettings);
                        if (!$payroll->save()) {
                            $flag = false;
                            GeneralHelper::flashFailed(Html::errorSummary($payroll));
                            break;
                        }
                        $resetCount++;
                    } else {
                        // Status DRAFT atau APPROVE tidak diubah
                        $skippedCount++;
                    }
                } else {
                    // Pegawai baru yang payroll belum digenerate
                    $payroll = new Payroll();
                    $this->populatePayrollData($payroll, $user, $periodStart, $periodEnd, $companyAllowances, $overtimeList, $scheduleList, $deductionSettings);
                    if (!$payroll->save()) {
                        $flag = false;
                        GeneralHelper::flashFailed(Html::errorSummary($payroll));
                        break;
                    }
                    $addedCount++;
                }
            }

            if ($flag) {
                $transaction->commit();
                $msg = 'Proses generate ulang berhasil.';
                $details = [];
                if ($resetCount > 0) {
                    $details[] = "$resetCount payroll pending di-reset";
                }
                if ($addedCount > 0) {
                    $details[] = "$addedCount pegawai baru ditambahkan";
                }
                if ($skippedCount > 0) {
                    $details[] = "$skippedCount pegawai dilewati (sudah Draft/Approve)";
                }
                if (!empty($details)) {
                    $msg .= ' (' . implode(', ', $details) . ')';
                }
                GeneralHelper::flashSucceed($msg);
            } else {
                $transaction->rollBack();
            }
        } catch (\Throwable $th) {
            $transaction->rollBack();
            throw $th;
        }

        return $this->redirect(['index', 'month' => $month, 'year' => $year]);
    }

    protected function populatePayrollData(
        Payroll $payroll,
        Account $user,
        string $periodStart,
        string $periodEnd,
        array $companyAllowances,
        array $overtimeList,
        array $scheduleList,
        array $deductionSettings
    ): void {
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

        $userSchedules = $scheduleList[$user->id_user] ?? [];
        $deductionResult = Payroll::calculateDeductionDetails($userSchedules, $deductionSettings);
        $payroll->dedection = (int) round($deductionResult['total']);
        if ($payroll->dedection > 0) {
            $payroll->dedection_description = 'Potongan Kehadiran / Terlambat';
        } else {
            $payroll->dedection_description = null;
        }

        // Set BPJS percentages jika diaktifkan di data user
        $payroll->bpjs_kesehatan = !empty($user->bpjs_kesehatan) ? Payroll::BPJS_KESEHATAN_PERCENT : 0;
        $payroll->bpjs_jht = !empty($user->bpjs_jht) ? Payroll::BPJS_JHT_PERCENT : 0;
        $payroll->bpjs_jp = !empty($user->bpjs_jp) ? Payroll::BPJS_JP_PERCENT : 0;

        $payroll->gross_salary = $payroll->basic_salary + $totalAllowance + $payroll->overtime;
        $payroll->ter = Payroll::getTER(Account::listPtkp()[$payroll->ptkp] ?? null, $payroll->gross_salary);
        $payroll->tax = (int) round($payroll->gross_salary * $payroll->ter);

        $totalBpjs = $payroll->getTotalBpjsAmount();
        $payroll->net_salary = max(0, (int) round($payroll->gross_salary - $payroll->tax - $payroll->dedection - $totalBpjs));
        $payroll->status = Payroll::STATUS_PENDING;
        $payroll->id_user_generate = $this->user->id_user;
        $payroll->id_user_verify = null;
        $payroll->user_verify_at = null;
        $payroll->id_user_approve = null;
        $payroll->user_approve_at = null;
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
                    GeneralHelper::flashSucceed('Payroll telah diverifikasi');
                } else {
                    GeneralHelper::flashFailed(Html::errorSummary($model));
                }
                $strtotime = strtotime($model->period_start);
                return $this->redirect(['index', 'month' => date('n', $strtotime), 'year' => date('Y', $strtotime)]);
            }
        }

        return $this->redirect(['index']);
    }

    public function actionCancelVerify($id)
    {
        if ($this->request->isPost) {
            $model = $this->findModel($id);

            if ($model->status === Payroll::STATUS_DRAFT) {
                $model->status = Payroll::STATUS_PENDING;
                $model->id_user_verify = null;
                $model->user_verify_at = null;
                if ($model->save()) {
                    GeneralHelper::flashSucceed('Verifikasi payroll telah dibatalkan, status kembali ke PENDING');
                } else {
                    GeneralHelper::flashFailed(Html::errorSummary($model));
                }
                $strtotime = strtotime($model->period_start);
                return $this->redirect(['index', 'month' => date('n', $strtotime), 'year' => date('Y', $strtotime)]);
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
                    GeneralHelper::flashSucceed('Payroll telah disetujui');
                } else {
                    GeneralHelper::flashFailed(Html::errorSummary($model));
                }
                $strtotime = strtotime($model->period_start);
                return $this->redirect(['index', 'month' => date('n', $strtotime), 'year' => date('Y', $strtotime)]);
            }
        }

        return $this->redirect(['index']);
    }

    public function actionCancelApprove($id)
    {
        if ($this->request->isPost) {
            if (!RoleHelper::approvalPayroll()) {
                throw new ForbiddenHttpException('You do not have permission to cancel payroll approval.');
            }

            $model = $this->findModel($id);

            if ($model->status === Payroll::STATUS_APPROVE) {
                $model->status = Payroll::STATUS_DRAFT;
                $model->id_user_approve = null;
                $model->user_approve_at = null;
                if ($model->save()) {
                    GeneralHelper::flashSucceed('Persetujuan payroll telah dibatalkan, status kembali ke DRAFT');
                } else {
                    GeneralHelper::flashFailed(Html::errorSummary($model));
                }
                $strtotime = strtotime($model->period_start);
                return $this->redirect(['index', 'month' => date('n', $strtotime), 'year' => date('Y', $strtotime)]);
            }
        }

        return $this->redirect(['index']);
    }

    public function actionUpdateDeduction($id)
    {
        $model = $this->findModel($id);

        if ($model->status !== Payroll::STATUS_PENDING) {
            if ($this->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => false,
                    'message' => 'Potongan gaji hanya dapat diubah saat status Pending.',
                ];
            }
            GeneralHelper::flashFailed('Potongan gaji hanya dapat diubah saat status Pending.');
            $month = date('n', strtotime($model->period_start));
            $year = date('Y', strtotime($model->period_start));
            return $this->redirect(['index', 'month' => $month, 'year' => $year]);
        }

        if ($this->request->isPost) {
            $dedection = $this->request->post('dedection', $model->dedection);
            if (is_string($dedection)) {
                $dedection = preg_replace('/[^0-9]/', '', $dedection);
            }
            $model->dedection = max(0, (int) $dedection);
            $description = $this->request->post('dedection_description');
            $model->dedection_description = !empty(trim((string) $description)) ? trim((string) $description) : null;

            $model->recalculateNetSalary();

            if ($model->save()) {
                if ($this->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return [
                        'success' => true,
                        'message' => 'Potongan gaji berhasil diperbarui.',
                        'dedection' => $model->dedection,
                        'dedection_formatted' => Yii::$app->formatter->asInteger($model->dedection),
                        'dedection_description' => $model->dedection_description,
                        'net_salary' => $model->net_salary,
                        'net_salary_formatted' => Yii::$app->formatter->asInteger($model->net_salary),
                    ];
                }
                GeneralHelper::flashSucceed('Potongan gaji berhasil diperbarui.');
            } else {
                if ($this->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return [
                        'success' => false,
                        'message' => Html::errorSummary($model),
                    ];
                }
                GeneralHelper::flashFailed(Html::errorSummary($model));
            }
        }

        $month = date('n', strtotime($model->period_start));
        $year = date('Y', strtotime($model->period_start));
        return $this->redirect(['index', 'month' => $month, 'year' => $year]);
    }

    public function actionSlip($id)
    {
        $model = $this->findModel($id);

        if ($model->status !== Payroll::STATUS_APPROVE) {
            throw new ForbiddenHttpException('Slip gaji hanya dapat diakses jika status payroll sudah APPROVE.');
        }

        if (!RoleHelper::allUser() && $model->id_user !== $this->user->id_user) {
            throw new ForbiddenHttpException('Anda tidak memiliki akses ke slip gaji ini.');
        }

        $content = $this->renderPartial('slip', [
            'model' => $model,
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
                'title' => 'Slip Gaji - ' . ($model->user ? $model->user->name : ''),
            ],
            'methods' => [
                'SetHeader' => ['Slip Gaji||Dicetak pada: ' . date('d/m/Y H:i')],
                'SetFooter' => ['|Halaman {PAGENO}|'],
            ]
        ]);

        return $pdf->render();
    }

    protected function findModel($id)
    {
        if (is_numeric($id)) {
            $model = Payroll::findOne(['id_payroll' => $id, 'id_company' => $this->id_company]);
        } else {
            $model = Payroll::findOne(['uuid' => $id, 'id_company' => $this->id_company]);
        }

        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
