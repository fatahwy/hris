<?php

/** @var yii\web\View $this */
/** @var app\models\master\Company $company */
/** @var array $sources */
/** @var array $statuses */
/** @var array $savedDeductions */

use app\models\master\Company;
use kartik\tabs\TabsX;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Setting';
$this->params['breadcrumbs'][] = $this->title;

$cutoffVal = (int) ($company->payroll_cutoff_date ?: 20);
$currentPeriod = $company->getPayrollPeriod(date('n'), date('Y'));

$statusBadges = [
    \app\models\trx\Schedule::STATUS_PRESENT_EARLY_CLOCK_OUT => [
        'bg' => 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25',
        'icon' => 'bi-clock-history',
        'desc' => 'Pemotongan saat karyawan pulang lebih awal dari jam kerja (berkelipatan menit).'
    ],
    \app\models\trx\Schedule::STATUS_PRESENT_LATE => [
        'bg' => 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25',
        'icon' => 'bi-alarm',
        'desc' => 'Pemotongan saat karyawan keterlambatan masuk (berkelipatan menit).'
    ],
    \app\models\trx\Schedule::STATUS_PRESENT_ABSENT => [
        'bg' => 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25',
        'icon' => 'bi-person-x',
        'desc' => 'Pemotongan saat karyawan tidak hadir / mangkir tanpa keterangan (per kejadian/hari).'
    ],
];
?>

<div class="setting-general-index">
    <div class="card modern-form-card shadow-sm border-0">
        <div class="card-header bg-white py-3 px-4 border-bottom">
            <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-sliders text-primary"></i> Pengaturan Umum &amp; Rumus Payroll
            </h5>
            <span class="text-muted small">Kelola tanggal cut off siklus penggajian dan formula otomatisasi potongan
                absensi</span>
        </div>
        <div class="card-body p-4">
            <?php $form = ActiveForm::begin([
                'id' => 'setting-general-form',
                'options' => ['class' => 'needs-validation'],
            ]); ?>

            <?php
            // TAB 1: Periode & Cut Off Payroll
            ob_start();
            ?>
            <div class="pt-3">
                <div class="card border rounded-3 shadow-none bg-white overflow-hidden mb-4">
                    <div
                        class="card-header bg-light py-3 px-3 px-sm-4 d-flex align-items-center justify-content-between border-bottom">
                        <div class="d-flex align-items-center gap-2 gap-sm-3">
                            <span
                                class="badge bg-primary bg-opacity-10 text-primary p-2 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                style="width: 38px; height: 38px;">
                                <i class="bi bi-calendar2-range fs-5"></i>
                            </span>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">Tanggal Cut Off Penggajian (Payroll)</h6>
                                <small class="text-muted">Menentukan batas awal dan akhir penarikan absensi, lembur, dan
                                    potongan gaji bulanan</small>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-3 p-sm-4">
                        <div class="row g-4">
                            <div class="col-md-5 col-lg-4">
                                <label for="payroll_cutoff_date" class="form-label fw-semibold small text-muted">Tanggal
                                    Cut Off (1 - 31)</label>
                                <div class="input-group">
                                    <span class="input-group-text small bg-light text-muted"><i
                                            class="bi bi-calendar-event"></i></span>
                                    <input type="number" id="payroll_cutoff_date" name="payroll_cutoff_date"
                                        class="form-control" value="<?= Html::encode($cutoffVal) ?>" min="1" max="31"
                                        required>
                                    <span class="input-group-text small bg-light text-muted">Setiap Bulan</span>
                                </div>
                                <div class="form-text small text-muted mt-2">
                                    <i class="bi bi-info-circle me-1"></i> Standar default sistem: Tanggal
                                    <strong>20</strong>
                                </div>
                            </div>

                            <div class="col-md-7 col-lg-8">
                                <div class="p-3 rounded-3 bg-light border">
                                    <div class="d-flex align-items-start gap-2">
                                        <i class="bi bi-lightbulb text-primary fs-5 mt-0.5 flex-shrink-0"></i>
                                        <div class="w-100">
                                            <strong class="text-dark d-block mb-1">Alur Perhitungan Periode:</strong>
                                            <p class="small text-muted mb-2">
                                                Ketika cut off diatur ke tanggal <span
                                                    class="badge bg-primary bg-opacity-10 text-primary fw-bold px-2 py-1"
                                                    id="cutoff-preview-val"><?= $cutoffVal ?></span>,
                                                maka data absensi &amp; payroll dihitung dari <strong>tanggal setelah
                                                    tanggal cut off bulan sebelumnya</strong>
                                                (tanggal <strong class="text-dark"
                                                    id="cutoff-prev-day"><?= min(31, $cutoffVal + 1) ?></strong>) hingga
                                                <strong>tanggal cut off bulan sekarang / filter</strong> (tanggal
                                                <strong class="text-dark"
                                                    id="cutoff-cur-day"><?= $cutoffVal ?></strong>).
                                            </p>
                                            <div class="pt-2 border-top small text-secondary">
                                                <i class="bi bi-calendar-check text-success me-1"></i> Contoh periode
                                                bulan filter saat ini (<?= date('F Y') ?>):
                                                <span class="fw-semibold text-dark ms-1" id="cutoff-sample-text">
                                                    <?= date('d M Y', strtotime($currentPeriod['start'])) ?> s/d
                                                    <?= date('d M Y', strtotime($currentPeriod['end'])) ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm">
                        <i class="bi bi-check-lg me-1"></i> Simpan Pengaturan
                    </button>
                </div>
            </div>
            <?php
            $cutoffContent = ob_get_clean();

            // TAB 2: Formula Potongan Absensi
            ob_start();
            ?>
            <div class="row g-4 pt-3">
                <?php foreach ($statuses as $statusKey => $statusLabel):
                    $badgeInfo = $statusBadges[$statusKey] ?? ['bg' => 'bg-info bg-opacity-10 text-info', 'icon' => 'bi-info-circle', 'desc' => ''];

                    $rawRule = $savedDeductions[$statusKey] ?? [];
                    if (isset($rawRule[0]) && is_array($rawRule[0])) {
                        $rule = $rawRule[0];
                    } else {
                        $rule = is_array($rawRule) ? $rawRule : [];
                    }

                    $currentSource = $rule['source'] ?? '';
                    $currentMinutes = $rule['minutes'] ?? ($rule['min_minutes'] ?? 0);
                    $currentAmount = $rule['amount'] ?? 0;
                    $isAbsent = ($statusKey === \app\models\trx\Schedule::STATUS_PRESENT_ABSENT);
                    ?>

                    <div class="col-12">
                        <div class="card border rounded-3 shadow-none bg-white overflow-hidden">
                            <div
                                class="card-header bg-light py-3 px-3 px-sm-4 d-flex align-items-center justify-content-between border-bottom">
                                <div class="d-flex align-items-center gap-2 gap-sm-3">
                                    <span
                                        class="badge <?= $badgeInfo['bg'] ?> p-2 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                        style="width: 38px; height: 38px;">
                                        <i class="bi <?= $badgeInfo['icon'] ?> fs-5"></i>
                                    </span>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark"><?= Html::encode($statusLabel) ?></h6>
                                        <small class="text-muted"><?= Html::encode($badgeInfo['desc']) ?></small>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body p-3 p-sm-4">
                                <div class="row g-3 align-items-end">
                                    <div class="<?= $isAbsent ? 'col-md-6' : 'col-md-5' ?>">
                                        <label class="form-label fw-semibold small text-muted">Sumber Pemotongan</label>
                                        <select name="deductions[<?= $statusKey ?>][source]" class="form-select">
                                            <option value="">-- Tidak Ada Pemotongan --</option>
                                            <?php foreach ($sources as $sourceKey => $sourceName): ?>
                                                <option value="<?= $sourceKey ?>" <?= (string) $currentSource === (string) $sourceKey ? 'selected' : '' ?>>
                                                    <?= Html::encode($sourceName) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <?php if (!$isAbsent): ?>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold small text-muted">Per Berapa Menit
                                                (Kelipatan)</label>
                                            <div class="input-group">
                                                <input type="number" name="deductions[<?= $statusKey ?>][minutes]"
                                                    class="form-control" value="<?= Html::encode($currentMinutes) ?>" min="0"
                                                    placeholder="30">
                                                <span class="input-group-text small bg-light text-muted">Menit</span>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <input type="hidden" name="deductions[<?= $statusKey ?>][minutes]" value="0">
                                    <?php endif; ?>

                                    <div class="<?= $isAbsent ? 'col-md-6' : 'col-md-4' ?>">
                                        <label class="form-label fw-semibold small text-muted">Nilai Potongan (Rp)</label>
                                        <div class="input-group">
                                            <span class="input-group-text small bg-light text-muted">Rp</span>
                                            <input type="number" step="any" name="deductions[<?= $statusKey ?>][amount]"
                                                class="form-control" value="<?= Html::encode($currentAmount) ?>" min="0"
                                                placeholder="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm">
                    <i class="bi bi-check-lg me-1"></i> Simpan Pengaturan
                </button>
            </div>
            <?php
            $payrollContent = ob_get_clean();

            echo TabsX::widget([
                'items' => [
                    [
                        'label' => '<i class="bi bi-calendar2-range me-2"></i>Periode &amp; Cut Off Payroll',
                        'content' => $cutoffContent,
                        'active' => true,
                    ],
                    // [
                    //     'label' => '<i class="bi bi-cash-stack me-2"></i>Formula Potongan Payroll',
                    //     'content' => $payrollContent,
                    //     'active' => false,
                    // ],
                ],
                'position' => TabsX::POS_ABOVE,
                'encodeLabels' => false,
            ]);
            ?>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php
$js = <<<JS
$('#payroll_cutoff_date').on('input change', function() {
    var val = parseInt($(this).val(), 10);
    if (isNaN(val) || val < 1) {
        val = 1;
    } else if (val > 28) {
        val = 28;
    }
    $('#cutoff-preview-val').text(val);
    $('#cutoff-prev-day').text(val >= 28 ? 1 : (val + 1));
    $('#cutoff-cur-day').text(val);
});
JS;
$this->registerJs($js);
?>