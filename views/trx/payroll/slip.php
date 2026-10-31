<?php

use app\helpers\GeneralHelper;
use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var app\models\trx\Payroll $model */
/** @var bool $isPdf */

$formatter = Yii::$app->formatter;

$user = $model->user;
$company = $model->company;
$department = $user && $user->department ? $user->department->name : '-';
$position = $user && $user->position ? $user->position->name : '-';
$employeeCode = $user ? $user->employee_code : '-';
$userName = $user ? $user->name : '-';
$companyName = $company ? $company->name : 'Perusahaan';

// Periode label: e.g. "Oktober 2026"
$monthYearLabel = date('F Y', strtotime($model->period_end));
$periodRangeLabel = date('d/m/Y', strtotime($model->period_start)) . ' - ' . date('d/m/Y', strtotime($model->period_end));
$bulanIndo = [
    'January' => 'Januari',
    'February' => 'Februari',
    'March' => 'Maret',
    'April' => 'April',
    'May' => 'Mei',
    'June' => 'Juni',
    'July' => 'Juli',
    'August' => 'Agustus',
    'September' => 'September',
    'October' => 'Oktober',
    'November' => 'November',
    'December' => 'Desember',
];
$periodLabel = strtr($monthYearLabel, $bulanIndo);

// Tanggal bayar: e.g. "25 Oktober 2026"
$payDateRaw = $model->user_approve_at ? date('d F Y', strtotime($model->user_approve_at)) : date('d F Y', strtotime($model->period_end));
$payDate = strtr($payDateRaw, $bulanIndo);

// Parse allowances
$allowanceItems = $model->allowance ?? [];
if (is_string($allowanceItems)) {
    $allowanceItems = json_decode($allowanceItems, true) ?? [];
}

// Incomes
$incomes = [];
if (!empty($model->basic_salary)) {
    $incomes[] = ['label' => 'Gaji pokok', 'amount' => (float) $model->basic_salary];
}
foreach ($allowanceItems as $item) {
    $val = (float) ($item['value'] ?? 0);
    if ($val > 0) {
        $incomes[] = [
            'label' => $item['name'] ?? 'Tunjangan',
            'amount' => $val,
        ];
    }
}
if (!empty($model->overtime) && (float) $model->overtime > 0) {
    $incomes[] = ['label' => 'Lembur', 'amount' => (float) $model->overtime];
}
$totalIncome = (float) $model->gross_salary;
if ($totalIncome <= 0 && !empty($incomes)) {
    $totalIncome = array_sum(array_column($incomes, 'amount'));
}

// Deductions
$deductions = [];
if (!empty($model->dedection) && (float) $model->dedection > 0) {
    $deductionLabel = !empty($model->dedection_description) ? $model->dedection_description : 'Potongan Kehadiran / Terlambat';
    $deductions[] = ['label' => $deductionLabel, 'amount' => (float) $model->dedection];
}
if (!empty($model->tax) && (float) $model->tax > 0) {
    $deductions[] = ['label' => 'PPh 21', 'amount' => (float) $model->tax];
}
$bpjsKesAmount = $model->getBpjsKesehatanAmount();
if ($bpjsKesAmount > 0) {
    $deductions[] = [
        'label' => 'BPJS Kesehatan (' . $model->getBpjsKesehatanPercent() . '%)',
        'amount' => $bpjsKesAmount,
    ];
}
$bpjsJhtAmount = $model->getBpjsJhtAmount();
if ($bpjsJhtAmount > 0) {
    $deductions[] = [
        'label' => 'BPJS Ketenagakerjaan - JHT (' . $model->getBpjsJhtPercent() . '%)',
        'amount' => $bpjsJhtAmount,
    ];
}
$bpjsJpAmount = $model->getBpjsJpAmount();
if ($bpjsJpAmount > 0) {
    $deductions[] = [
        'label' => 'BPJS Ketenagakerjaan - JP (' . $model->getBpjsJpPercent() . '%)',
        'amount' => $bpjsJpAmount,
    ];
}
$totalDeduction = (float) $model->dedection + (float) $model->tax + $bpjsKesAmount + $bpjsJhtAmount + $bpjsJpAmount;

// Terbilang
$terbilangText = GeneralHelper::terbilang($model->net_salary) . ' rupiah';

// Inisial perusahaan untuk avatar
$companyWords = explode(' ', trim($companyName));
$companyInitials = '';
if (count($companyWords) >= 2) {
    $companyInitials = strtoupper(substr($companyWords[0], 0, 1) . substr($companyWords[1], 0, 1));
} else {
    $companyInitials = strtoupper(substr($companyName, 0, 2));
}
?>

<?php if (empty($isPdf)): ?>
    <style>
        .slip-box {
            padding: 28px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
            border-radius: 12px;
        }
        @media (max-width: 650px) {
            .slip-box {
                padding: 16px 12px !important;
                border-radius: 8px;
            }
            .slip-header-table td {
                padding: 4px 0 !important;
            }
            .slip-header-title {
                text-align: left !important;
                margin-top: 8px !important;
            }
            .slip-info-table td {
                padding: 4px 2px !important;
                font-size: 12.5px !important;
            }
            .slip-columns-table, .slip-columns-table > tbody, .slip-columns-table > tbody > tr, .slip-columns-table > tbody > tr > td {
                display: block !important;
                width: 100% !important;
            }
            .slip-spacer-td {
                display: block !important;
                height: 18px !important;
                width: 100% !important;
            }
        }
    </style>
    <div style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()"
            style="padding: 9px 20px; background-color: #1a5653; color: #ffffff; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 600; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <i class="fa fa-print"></i> Cetak / Simpan PDF
        </button>
    </div>
<?php endif; ?>

<div class="slip-box" style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333333; max-width: 780px; margin: 0 auto; background-color: #ffffff;">
    <!-- Header -->
    <div style="width: 100%; margin-bottom: 12px;">
        <table class="slip-header-table" style="width: 100%; border-collapse: collapse; border: none;">
            <tr>
                <td style="width: 50px; vertical-align: middle; padding: 0;">
                    <div style="width: 44px; height: 44px; background-color: #1a5653; color: #ffffff; font-size: 16px; font-weight: bold; text-align: center; line-height: 44px; border-radius: 6px;">
                        <?= Html::encode($companyInitials) ?>
                    </div>
                </td>
                <td style="vertical-align: middle; padding-left: 12px; padding-top: 0; padding-bottom: 0;">
                    <div style="font-size: 18px; font-weight: 700; color: #1e293b; line-height: 1.2; margin: 0 0 3px 0;">
                        <?= Html::encode($companyName) ?>
                    </div>
                    <div style="font-size: 13px; color: #64748b; line-height: 1.2; margin: 0;">
                        Slip gaji karyawan
                    </div>
                </td>
                <td class="slip-header-title" style="text-align: right; vertical-align: middle; padding: 0;">
                    <div style="font-size: 20px; font-weight: 800; letter-spacing: 0.5px; color: #0f172a; line-height: 1.2; margin: 0 0 3px 0;">
                        SLIP GAJI
                    </div>
                    <div style="font-size: 13px; color: #64748b; line-height: 1.2; margin: 0;">
                        Periode <?= Html::encode($periodLabel) ?> <span style="font-size: 11.5px; color: #94a3b8;">(<?= Html::encode($periodRangeLabel) ?>)</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Divider Line -->
    <div style="height: 3px; background-color: #1a5653; margin: 15px 0 20px 0; width: 100%;"></div>

    <!-- Info Karyawan -->
    <table class="slip-info-table" style="width: 100%; border-collapse: collapse; margin-bottom: 25px; font-size: 13.5px; border: none;">
        <tr>
            <td style="color: #64748b; width: 90px; padding: 5px 0; vertical-align: top;">Nama</td>
            <td style="font-weight: 600; color: #1e293b; padding: 5px 0; vertical-align: top; width: 260px;"><?= Html::encode($userName) ?></td>
            <td style="color: #64748b; width: 120px; padding: 5px 0 5px 15px; vertical-align: top;">Tanggal bayar</td>
            <td style="font-weight: 600; color: #1e293b; padding: 5px 0; vertical-align: top;"><?= Html::encode($payDate) ?></td>
        </tr>
        <tr>
            <td style="color: #64748b; width: 90px; padding: 5px 0; vertical-align: top;">NIK</td>
            <td style="font-weight: 600; color: #1e293b; padding: 5px 0; vertical-align: top;"><?= Html::encode($employeeCode) ?></td>
            <td style="color: #64748b; width: 110px; padding: 5px 0 5px 15px; vertical-align: top;">Divisi</td>
            <td style="font-weight: 600; color: #1e293b; padding: 5px 0; vertical-align: top;"><?= Html::encode($department) ?></td>
        </tr>
        <tr>
            <td style="color: #64748b; width: 90px; padding: 5px 0; vertical-align: top;">Jabatan</td>
            <td style="font-weight: 600; color: #1e293b; padding: 5px 0; vertical-align: top;"><?= Html::encode($position) ?></td>
            <td style="padding: 5px 0 5px 15px; vertical-align: top;"></td>
            <td style="padding: 5px 0; vertical-align: top;"></td>
        </tr>
    </table>

    <!-- Pendapatan & Potongan -->
    <table class="slip-columns-table" style="width: 100%; border-collapse: collapse; margin-bottom: 25px; border: none;">
        <tr>
            <!-- Kolom Pendapatan -->
            <td style="width: 48%; vertical-align: top; padding: 0;">
                <table style="width: 100%; border-collapse: collapse; font-size: 13.5px; border: none;">
                    <tr>
                        <td colspan="2" style="font-size: 14px; font-weight: 700; color: #0d9488; padding-bottom: 8px; border-bottom: 1.5px solid #cbd5e1; margin-bottom: 10px;">Pendapatan</td>
                    </tr>
                    <?php if (!empty($incomes)): ?>
                        <?php foreach ($incomes as $inc): ?>
                            <tr>
                                <td style="padding: 6px 0; border-bottom: 1px dotted #e2e8f0; color: #333333;"><?= Html::encode($inc['label']) ?></td>
                                <td style="padding: 6px 0; border-bottom: 1px dotted #e2e8f0; text-align: right; color: #333333;">Rp <?= number_format($inc['amount'], 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td style="padding: 6px 0; border-bottom: 1px dotted #e2e8f0; color: #333333;">-</td>
                            <td style="padding: 6px 0; border-bottom: 1px dotted #e2e8f0; text-align: right; color: #333333;">Rp 0</td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <td style="padding: 10px 0 0 0; border-top: 1.5px solid #94a3b8; font-weight: 700; color: #0f172a;">Total pendapatan</td>
                        <td style="padding: 10px 0 0 0; border-top: 1.5px solid #94a3b8; font-weight: 700; text-align: right; color: #0f172a;">Rp <?= number_format($totalIncome, 0, ',', '.') ?></td>
                    </tr>
                </table>
            </td>

            <!-- Spacer -->
            <td class="slip-spacer-td" style="width: 4%; padding: 0;"></td>

            <!-- Kolom Potongan -->
            <td style="width: 48%; vertical-align: top; padding: 0;">
                <table style="width: 100%; border-collapse: collapse; font-size: 13.5px; border: none;">
                    <tr>
                        <td colspan="2" style="font-size: 14px; font-weight: 700; color: #b91c1c; padding-bottom: 8px; border-bottom: 1.5px solid #cbd5e1; margin-bottom: 10px;">Potongan</td>
                    </tr>
                <?php if (!empty($deductions)): ?>
                        <?php foreach ($deductions as $ded): ?>
                            <tr>
                                <td style="padding: 6px 0; border-bottom: 1px dotted #e2e8f0; color: #333333;"><?= Html::encode($ded['label']) ?></td>
                                <td style="padding: 6px 0; border-bottom: 1px dotted #e2e8f0; text-align: right; color: #333333;">Rp <?= number_format($ded['amount'], 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td style="padding: 6px 0; border-bottom: 1px dotted #e2e8f0; color: #94a3b8; font-style: italic;">Tidak ada potongan</td>
                            <td style="padding: 6px 0; border-bottom: 1px dotted #e2e8f0; text-align: right; color: #333333;">Rp 0</td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <td style="padding: 10px 0 0 0; border-top: 1.5px solid #94a3b8; font-weight: 700; color: #0f172a;">Total potongan</td>
                        <td style="padding: 10px 0 0 0; border-top: 1.5px solid #94a3b8; font-weight: 700; text-align: right; color: #0f172a;">- Rp <?= number_format($totalDeduction, 0, ',', '.') ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Gaji Bersih Box -->
    <div style="background-color: #e6f3f1; border-radius: 8px; padding: 16px 20px; margin-top: 10px; margin-bottom: 15px;">
        <div style="font-size: 12.5px; color: #475569; margin-bottom: 4px;">Gaji bersih diterima</div>
        <div style="font-size: 26px; font-weight: 800; color: #0f4c47; letter-spacing: -0.5px; line-height: 1.1;">
            Rp <?= number_format($model->net_salary, 0, ',', '.') ?>
        </div>
    </div>

    <!-- Terbilang -->
    <div style="font-size: 12.5px; font-style: italic; color: #64748b; line-height: 1.4;">
        Terbilang: <?= Html::encode($terbilangText) ?>
    </div>
</div>
