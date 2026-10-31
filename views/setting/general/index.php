<?php

/** @var yii\web\View $this */
/** @var app\models\master\Company $company */
/** @var array $sources */
/** @var array $statuses */
/** @var array $savedDeductions */

use kartik\tabs\TabsX;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Setting';
$this->params['breadcrumbs'][] = $this->title;

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
                <i class="bi bi-sliders text-primary"></i> Pengaturan Sistem &amp; Rumus Payroll
            </h5>
            <span class="text-muted small">Atur formula otomatisasi potongan absensi, keterlambatan, dan pulang lebih awal</span>
        </div>
        <div class="card-body p-4">
            <?php $form = ActiveForm::begin([
                'id' => 'setting-deduction-form',
                'options' => ['class' => 'needs-validation'],
            ]); ?>

            <?php ob_start(); ?>
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
                            <div class="card-header bg-light py-3 px-3 px-sm-4 d-flex align-items-center justify-content-between border-bottom">
                                <div class="d-flex align-items-center gap-2 gap-sm-3">
                                    <span class="badge <?= $badgeInfo['bg'] ?> p-2 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
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
                                            <label class="form-label fw-semibold small text-muted">Per Berapa Menit (Kelipatan)</label>
                                            <div class="input-group">
                                                <input type="number" name="deductions[<?= $statusKey ?>][minutes]" class="form-control"
                                                    value="<?= Html::encode($currentMinutes) ?>" min="0" placeholder="30">
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
                        'label' => '<i class="bi bi-cash-stack me-2"></i>Formula Potongan Payroll',
                        'content' => $payrollContent,
                        'active' => true,
                    ],
                ],
                'position' => TabsX::POS_ABOVE,
                'encodeLabels' => false,
            ]);
            ?>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
