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
// $this->params['breadcrumbs'][] = ['label' => 'Setting', 'url' => '#'];
$this->params['breadcrumbs'][] = $this->title;

$statusBadges = [
    \app\models\trx\Schedule::STATUS_PRESENT_EARLY_CLOCK_OUT => [
        'bg' => 'bg-warning text-dark',
        'icon' => 'bi-clock-history',
        'desc' => 'Pemotongan saat karyawan pulang lebih awal dari jam kerja (berkelipatan menit).'
    ],
    \app\models\trx\Schedule::STATUS_PRESENT_LATE => [
        'bg' => 'bg-danger text-white',
        'icon' => 'bi-alarm',
        'desc' => 'Pemotongan saat karyawan keterlambatan masuk (berkelipatan menit).'
    ],
    \app\models\trx\Schedule::STATUS_PRESENT_ABSENT => [
        'bg' => 'bg-secondary text-white',
        'icon' => 'bi-person-x',
        'desc' => 'Pemotongan saat karyawan tidak hadir / mangkir tanpa keterangan (per kejadian/hari).'
    ],
];
?>

<div class="setting-general-index">
    <?php $form = ActiveForm::begin([
        'id' => 'setting-deduction-form',
        'options' => ['class' => 'needs-validation'],
    ]); ?>

    <?php ob_start(); ?>
    <div class="row g-4 pt-3">
        <?php foreach ($statuses as $statusKey => $statusLabel):
            $badgeInfo = $statusBadges[$statusKey] ?? ['bg' => 'bg-info text-white', 'icon' => 'bi-info-circle', 'desc' => ''];

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
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h4 class="fw-bold m-0 text-dark">
                        <i class="bi bi-sliders me-2 text-primary"></i><?= Html::encode($this->title) ?>
                    </h4>
                    <p class="text-muted small mb-0 mt-1">
                        Atur formula pemotongan gaji & tunjangan karyawan berdasarkan status kehadiran.
                    </p>
                </div>
            </div>

            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                    <div
                        class="card-header bg-white py-3 px-4 d-flex align-items-center justify-content-between border-bottom">
                        <div class="d-flex align-items-center gap-3">
                            <span
                                class="badge <?= $badgeInfo['bg'] ?> p-2.5 rounded-3 d-flex align-items-center justify-content-center"
                                style="width: 38px; height: 38px;">
                                <i class="bi <?= $badgeInfo['icon'] ?> fs-5"></i>
                            </span>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark"><?= Html::encode($statusLabel) ?></h6>
                                <small class="text-muted"><?= Html::encode($badgeInfo['desc']) ?></small>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
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
                                            value="<?= Html::encode($currentMinutes) ?>" min="0" placeholder="misal: 30">
                                        <span class="input-group-text">Menit</span>
                                    </div>
                                </div>
                            <?php else: ?>
                                <input type="hidden" name="deductions[<?= $statusKey ?>][minutes]" value="0">
                            <?php endif; ?>

                            <div class="<?= $isAbsent ? 'col-md-6' : 'col-md-4' ?>">
                                <label class="form-label fw-semibold small text-muted">Nilai Potongan (Rp)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
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

    <div class="d-flex justify-content-end mt-4 mb-3">
        <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm">
            <i class="bi bi-check-lg me-1"></i> Simpan
        </button>
    </div>
    <?php
    $payrollContent = ob_get_clean();

    echo TabsX::widget([
        'items' => [
            [
                'label' => '<i class="bi bi-cash-stack me-2"></i>Payroll',
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