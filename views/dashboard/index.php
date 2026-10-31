<?php

/** @var yii\web\View $this */
/** @var app\models\trx\Schedule[] $recentAttendances */
/** @var string $dateRange */
/** @var int $totalEmployees */
/** @var int $presentInPeriod */
/** @var int $pendingApprovals */
/** @var int $monthlyPayroll */

use app\models\trx\Schedule;
use yii\helpers\Html;
use kartik\daterange\DateRangePicker;

$formatter = Yii::$app->formatter;
?>
<div class="site-index pb-4">
    <!-- Header with Date Filter -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h4 mb-1 text-dark fw-bold d-flex align-items-center gap-2">
                <i class="bi bi-grid-fill text-primary fs-5"></i> Dashboard Overview
            </h1>
            <p class="text-muted small mb-0">Ringkasan performa kehadiran, persetujuan, dan penggajian karyawan.</p>
        </div>
        <div class="bg-white px-3 py-2 rounded-3 shadow-sm border d-flex align-items-center gap-2" style="min-width: 260px;">
            <i class="bi bi-calendar3 text-primary"></i>
            <form action="<?= \yii\helpers\Url::to(['dashboard/index']) ?>" method="get" id="date-filter-form" class="flex-grow-1 m-0">
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
                        'placeholder' => 'Pilih Periode Tanggal',
                        'class' => 'form-control border-0 bg-transparent p-0 small fw-semibold text-dark shadow-none',
                        'style' => 'cursor: pointer;',
                        'onchange' => 'this.form.submit()'
                    ],
                ]);
                ?>
            </form>
        </div>
    </div>

    <!-- Analytics Cards -->
    <div class="d-flex align-items-center justify-content-between mb-3">
        <span class="text-uppercase text-muted fw-bold small" style="letter-spacing: 0.05em;">
            <i class="bi bi-bar-chart-line me-1 text-primary"></i> Analytics Over Period
        </span>
    </div>

    <div class="row g-3 g-xl-4 mb-4 mb-xl-5">
        <!-- Card 1: Total Pegawai -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border shadow-sm rounded-4 bg-white" style="transition: all 0.2s ease;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="text-muted fw-semibold small">Total Pegawai</span>
                        <div class="rounded-3 d-flex align-items-center justify-content-center"
                            style="width: 44px; height: 44px; background: rgba(43, 95, 225, 0.1); color: #2b5fe1;">
                            <i class="bi bi-people-fill fs-5"></i>
                        </div>
                    </div>
                    <div class="h2 fw-bold text-dark mb-1"><?= $formatter->asInteger($totalEmployees) ?></div>
                    <div class="small text-muted d-flex align-items-center gap-1">
                        <i class="bi bi-person-check text-success"></i> Terdaftar aktif
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Kehadiran -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border shadow-sm rounded-4 bg-white" style="transition: all 0.2s ease;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="text-muted fw-semibold small">Kehadiran Periode Ini</span>
                        <div class="rounded-3 d-flex align-items-center justify-content-center"
                            style="width: 44px; height: 44px; background: rgba(16, 185, 129, 0.1); color: #10b981;">
                            <i class="bi bi-check2-circle fs-5"></i>
                        </div>
                    </div>
                    <div class="h2 fw-bold text-dark mb-1"><?= $formatter->asInteger($presentInPeriod) ?></div>
                    <div class="small text-muted d-flex align-items-center gap-1">
                        <i class="bi bi-calendar-check text-success"></i> Total presensi tercatat
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Pending Approvals -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border shadow-sm rounded-4 bg-white" style="transition: all 0.2s ease;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="text-muted fw-semibold small">Pending Approvals</span>
                        <div class="rounded-3 d-flex align-items-center justify-content-center"
                            style="width: 44px; height: 44px; background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                            <i class="bi bi-clock-history fs-5"></i>
                        </div>
                    </div>
                    <div class="h2 fw-bold <?= $pendingApprovals > 0 ? 'text-warning' : 'text-dark' ?> mb-1">
                        <?= $formatter->asInteger($pendingApprovals) ?>
                    </div>
                    <div class="small text-muted d-flex align-items-center gap-1">
                        <i class="bi bi-hourglass-split text-warning"></i> Pengajuan cuti &amp; izin
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: Monthly Payroll -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border shadow-sm rounded-4 bg-white" style="transition: all 0.2s ease;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="text-muted fw-semibold small">Monthly Payroll</span>
                        <div class="rounded-3 d-flex align-items-center justify-content-center"
                            style="width: 44px; height: 44px; background: rgba(99, 102, 241, 0.1); color: #6366f1;">
                            <i class="bi bi-cash-stack fs-5"></i>
                        </div>
                    </div>
                    <div class="h3 fw-bold text-dark mb-1 text-truncate" title="<?= $formatter->asCurrency($monthlyPayroll) ?>">
                        <?= $formatter->asCurrency($monthlyPayroll) ?>
                    </div>
                    <div class="small text-muted d-flex align-items-center gap-1">
                        <i class="bi bi-wallet2 text-primary"></i> Estimasi penggajian
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Attendances Section -->
    <div class="card modern-grid-card shadow-sm border-0">
        <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom">
            <div>
                <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-clock text-primary"></i> Recent Attendances
                </h5>
                <span class="text-muted small">Aktivitas presensi karyawan terkini</span>
            </div>
            <a href="<?= \yii\helpers\Url::to(['/trx/attendance']) ?>" class="btn btn-sm btn-outline-primary rounded-3 px-3">
                Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-nowrap">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 px-4">Nama Pegawai</th>
                        <th class="py-3">Shift</th>
                        <th class="py-3">Jadwal Masuk</th>
                        <th class="py-3">Status</th>
                        <th class="py-3 text-end px-4">Waktu Presensi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentAttendances as $attendance): ?>
                        <tr>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-primary"
                                        style="width: 36px; height: 36px; background-color: rgba(43, 95, 225, 0.1); font-size: 0.85rem; flex-shrink: 0;">
                                        <?= strtoupper(substr($attendance->user->name ?? 'U', 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark"><?= Html::encode($attendance->user->name ?? '-') ?></div>
                                        <small class="text-muted"><?= Html::encode($attendance->user->employee_code ?? '') ?></small>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3">
                                <span class="badge bg-light text-dark border px-2.5 py-1.5 fw-medium">
                                    <?= Html::encode($attendance->shift_name) ?>
                                </span>
                            </td>
                            <td class="py-3 text-muted small fw-medium">
                                <?= Yii::$app->formatter->asDatetime($attendance->workhour_start) ?>
                            </td>
                            <td class="py-3">
                                <?= Schedule::getLabelChip($attendance); ?>
                            </td>
                            <td class="text-end px-4 py-3">
                                <span class="fw-medium text-dark">
                                    <?= $attendance->checkin_datetime ? date('H:i', strtotime($attendance->checkin_datetime)) : '-' ?>
                                </span>
                                <span class="text-muted mx-1">/</span>
                                <span class="fw-medium text-dark">
                                    <?= $attendance->checkout_datetime ? date('H:i', strtotime($attendance->checkout_datetime)) : '-' ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($recentAttendances)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted mb-2"><i class="bi bi-inbox fs-1 opacity-50"></i></div>
                                <div class="text-secondary fw-medium">Tidak ada riwayat kehadiran pada periode ini</div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
