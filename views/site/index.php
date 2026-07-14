<?php

/** @var yii\web\View $this */
/** @var app\models\trx\Schedule[] $recentAttendances */

use app\models\trx\Schedule;
use yii\helpers\Html;
use kartik\daterange\DateRangePicker;

?>
<div class="site-index pb-4">
    <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
        <h2 class="h4 mb-0 text-primary fw-bold d-flex align-items-center gap-2">
            Dashboard
        </h2>
        <div class="bg-white p-2 rounded-3 shadow-sm border border-light" style="min-width: 300px;">
            <form action="<?= \yii\helpers\Url::to(['site/index']) ?>" method="get" id="date-filter-form">
                <?php
                echo DateRangePicker::widget([
                    'name' => 'date_range',
                    'value' => $dateRange,
                    'convertFormat' => true,
                    'pluginOptions' => [
                        'locale' => [
                            'format' => 'Y-m-d',
                            'separator' => ' - ',
                        ],
                        'opens' => 'left'
                    ],
                    'options' => [
                        'placeholder' => 'Pilih Tanggal',
                        'class' => 'form-control border-0 bg-transparent fw-medium',
                        'style' => 'cursor: pointer;',
                        'onchange' => 'this.form.submit()'
                    ],
                ]);
                ?>
            </form>
        </div>
    </div>

    <div class="text-uppercase text-secondary fw-bold mb-3 mt-4" style="font-size: 0.75rem; letter-spacing: 0.5px;">
        Analytics Over Period</div>

    <div class="row g-4 mb-5">
        <!-- Card 1 -->
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-light h-100 bg-white" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <div class="text-secondary fw-semibold mb-3 py-1" style="font-size: 0.95rem;">Total Pegawai</div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h2 class="fw-bold text-dark mb-0"><?= number_format($totalEmployees) ?></h2>
                        <div class="bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center"
                            style="width: 50px; height: 50px; border-radius: 14px;">
                            <i class="bi bi-people fs-4"></i>
                        </div>
                    </div>
                    <div class="text-secondary mb-1 mt-3" style="font-size: 0.85rem;">Total Pegawai Terdaftar</div>
                </div>
            </div>
        </div>
        <!-- Card 2 -->
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-light h-100 bg-white" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <div class="text-secondary fw-semibold mb-3 py-1" style="font-size: 0.95rem;">Kehadiran
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h2 class="fw-bold text-dark mb-0"><?= number_format($presentInPeriod) ?></h2>
                        <div class="bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center"
                            style="width: 50px; height: 50px; border-radius: 14px;">
                            <i class="bi bi-clipboard-check fs-4"></i>
                        </div>
                    </div>
                    <div class="text-secondary mb-1 mt-3" style="font-size: 0.85rem;">Total Absensi</div>
                </div>
            </div>
        </div>
        <!-- Card 3 -->
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-light h-100 bg-white" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <div class="text-secondary fw-semibold mb-3 py-1" style="font-size: 0.95rem;">Pending Approvals
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h2 class="fw-bold text-dark mb-0"><?= number_format($pendingApprovals) ?></h2>
                        <div class="bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center"
                            style="width: 50px; height: 50px; border-radius: 14px;">
                            <i class="bi bi-clock fs-4"></i>
                        </div>
                    </div>
                    <div class="text-secondary mb-1 mt-3" style="font-size: 0.85rem;">Pengajuan Cuti &amp; izin
                    </div>
                </div>
            </div>
        </div>
        <!-- Card 4 -->
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm border-light h-100 bg-white" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <div class="text-secondary fw-semibold mb-3 py-1" style="font-size: 0.95rem;">Monthly Payroll</div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h2 class="fw-bold text-dark mb-0" style="font-size: 1.5rem;">Rp
                            <?= number_format($monthlyPayroll, 0, ',', '.') ?>
                        </h2>
                        <div class="bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center"
                            style="width: 50px; height: 50px; border-radius: 14px;">
                            <i class="bi bi-currency-dollar fs-4"></i>
                        </div>
                    </div>
                    <div class="text-secondary mb-1 mt-3" style="font-size: 0.85rem;">Total Penggajian</div>
                </div>
            </div>
        </div>
    </div>

    <div class="text-uppercase text-secondary fw-bold mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px;">
        Recent Attendances
    </div>

    <?php $nowStr = date('Y-m-d H:i:s'); ?>
    <div class="table-responsive bg-white rounded-4 shadow-sm px-2 pb-2">
        <table class="table table-hover align-middle border-bottom-0 mb-0">
            <thead>
                <tr class="text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.8px;">
                    <th class="border-bottom fw-semibold py-3 px-4 bg-transparent text-secondary border-light">Nama</th>
                    <th class="border-bottom fw-semibold py-3 bg-transparent text-secondary border-light">Shift</th>
                    <th class="border-bottom fw-semibold py-3 bg-transparent text-secondary border-light">Tanggal</th>
                    <th class="border-bottom fw-semibold py-3 bg-transparent text-secondary border-light">Status</th>
                    <th class="border-bottom fw-semibold py-3 bg-transparent text-end px-4 border-light">Waktu</th>
                </tr>
            </thead>
            <tbody style="border-top: none;">
                <?php foreach ($recentAttendances as $attendance): ?>
                    <tr>
                        <td class="fw-medium border-light px-4 py-3 text-secondary text-nowrap">
                            <i
                                class="bi bi-person-circle text-primary bg-primary bg-opacity-10 p-2 rounded fs-6 me-3 align-middle"></i>
                            <?= Html::encode($attendance->user->name ?? '-') ?>
                        </td>
                        <td class="border-light py-3">
                            <?= Html::encode($attendance->shift_name) ?>
                        </td>
                        <td class="text-muted border-light small py-3 fw-medium">
                            <?= Yii::$app->formatter->asDatetime($attendance->workhour_start) ?>
                        </td>
                        <td class="border-light small py-3 fw-medium">
                            <?= Schedule::getLabelChip($attendance); ?>
                        </td>
                        <td class="text-end border-light text-muted px-4 py-3">
                            <?= $attendance->checkin_datetime ? date('H:i', strtotime($attendance->checkin_datetime)) : '-' ?>
                            -
                            <?= $attendance->checkout_datetime ? date('H:i', strtotime($attendance->checkout_datetime)) : '-' ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($recentAttendances)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No recent attendances found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>