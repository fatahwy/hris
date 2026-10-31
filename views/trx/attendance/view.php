<?php

use app\models\trx\Schedule;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var app\models\trx\Schedule $model */

$this->title = 'Kehadiran: ' . ($model->user->name ?? '-') . ' (' . date('d M Y', strtotime($model->date)) . ')';
$this->params['breadcrumbs'][] = ['label' => 'Kehadiran', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Detail';
?>

<div class="schedule-view">
    <div class="d-flex justify-content-end mb-3">
        <?= Html::a('<i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar', ['index'], ['class' => 'btn btn-outline-secondary px-3 shadow-sm']) ?>
    </div>

    <!-- Informasi Jadwal Card -->
    <div class="card modern-form-card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-calendar3 text-primary"></i> Informasi Jadwal &amp; Pegawai
            </h5>
            <div>
                <?= Schedule::getLabelChip($model); ?>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <?= DetailView::widget([
                    'model' => $model,
                    'options' => ['class' => 'table table-modern-detail mb-0'],
                    'attributes' => [
                        [
                            'label' => 'Nama Pegawai',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return '<div class="fw-bold text-dark fs-6">' . Html::encode($model->user->name ?? '-') . '</div>' .
                                       '<span class="text-muted small">NIK / Kode: ' . Html::encode($model->user->employee_code ?? '-') . '</span>';
                            }
                        ],
                        'date:date',
                        [
                            'attribute' => 'shift_name',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return '<span class="badge bg-light text-dark border px-2.5 py-1.5"><i class="bi bi-clock me-1 text-primary"></i>' . Html::encode($model->shift_name) . '</span>';
                            }
                        ],
                        'checkin_start:datetime',
                        'workhour_end:datetime',
                    ],
                ]) ?>
            </div>
        </div>
    </div>

    <!-- Clock In & Clock Out Section -->
    <div class="row g-4">
        <!-- Clock In Section -->
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm border-0 rounded-4 h-100 overflow-hidden bg-white">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 d-flex align-items-center justify-content-center"
                            style="width: 36px; height: 36px; background: rgba(16, 185, 129, 0.1); color: #10b981;">
                            <i class="bi bi-box-arrow-in-right fs-5"></i>
                        </div>
                        <h6 class="fw-bold mb-0 text-dark">Clock In (Masuk)</h6>
                    </div>
                    <?php if ($model->checkin_datetime): ?>
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1">Tercatat</span>
                    <?php else: ?>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary px-2.5 py-1">Belum Ada</span>
                    <?php endif; ?>
                </div>
                <div class="card-body p-4">
                    <?php if ($model->checkin_datetime): ?>
                        <div class="d-flex align-items-center gap-2 mb-3 p-3 bg-light rounded-3 border">
                            <i class="bi bi-clock text-success fs-5"></i>
                            <div>
                                <small class="text-muted d-block">Waktu Presensi</small>
                                <span class="fw-bold text-dark"><?= Yii::$app->formatter->asDatetime($model->checkin_datetime) ?></span>
                            </div>
                        </div>

                        <?php if ($model->checkin_photo): ?>
                            <div class="mb-3 text-center">
                                <label class="small text-muted fw-semibold d-block text-start mb-1">Foto Bukti Kehadiran:</label>
                                <?= Html::img('@web/' . $model->checkin_photo, [
                                    'class' => 'img-fluid rounded-3 shadow-sm border', 
                                    'style' => 'max-height: 260px; object-fit: cover;'
                                ]) ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($model->checkin_lat && $model->checkin_long): ?>
                            <div class="mb-2">
                                <small class="text-muted d-block mb-1"><i class="bi bi-geo-alt-fill text-danger me-1"></i> Koordinat Lokasi:</small>
                                <code class="small text-secondary"><?= Html::encode($model->checkin_lat) ?>, <?= Html::encode($model->checkin_long) ?></code>
                            </div>
                            <div class="ratio ratio-16x9 rounded-3 overflow-hidden border shadow-sm">
                                <iframe
                                    src="https://maps.google.com/maps?q=<?= rawurlencode($model->checkin_lat) ?>,<?= rawurlencode($model->checkin_long) ?>&z=15&output=embed"
                                    frameborder="0" style="border:0;" allowfullscreen>
                                </iframe>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-clock-slash fs-1 opacity-50 mb-2 d-block"></i>
                            <div class="fw-medium">Pegawai belum melakukan clock in</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Clock Out Section -->
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm border-0 rounded-4 h-100 overflow-hidden bg-white">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 d-flex align-items-center justify-content-center"
                            style="width: 36px; height: 36px; background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                            <i class="bi bi-box-arrow-right fs-5"></i>
                        </div>
                        <h6 class="fw-bold mb-0 text-dark">Clock Out (Pulang)</h6>
                    </div>
                    <?php if ($model->checkout_datetime): ?>
                        <span class="badge bg-warning bg-opacity-10 text-dark border border-warning border-opacity-25 px-2.5 py-1">Tercatat</span>
                    <?php else: ?>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary px-2.5 py-1">Belum Ada</span>
                    <?php endif; ?>
                </div>
                <div class="card-body p-4">
                    <?php if ($model->checkout_datetime): ?>
                        <div class="d-flex align-items-center gap-2 mb-3 p-3 bg-light rounded-3 border">
                            <i class="bi bi-clock text-warning fs-5"></i>
                            <div>
                                <small class="text-muted d-block">Waktu Presensi</small>
                                <span class="fw-bold text-dark"><?= Yii::$app->formatter->asDatetime($model->checkout_datetime) ?></span>
                            </div>
                        </div>

                        <?php if ($model->checkout_photo): ?>
                            <div class="mb-3 text-center">
                                <label class="small text-muted fw-semibold d-block text-start mb-1">Foto Bukti Kepulangan:</label>
                                <?= Html::img('@web/' . $model->checkout_photo, [
                                    'class' => 'img-fluid rounded-3 shadow-sm border', 
                                    'style' => 'max-height: 260px; object-fit: cover;'
                                ]) ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($model->checkout_lat && $model->checkout_long): ?>
                            <div class="mb-2">
                                <small class="text-muted d-block mb-1"><i class="bi bi-geo-alt-fill text-danger me-1"></i> Koordinat Lokasi:</small>
                                <code class="small text-secondary"><?= Html::encode($model->checkout_lat) ?>, <?= Html::encode($model->checkout_long) ?></code>
                            </div>
                            <div class="ratio ratio-16x9 rounded-3 overflow-hidden border shadow-sm">
                                <iframe
                                    src="https://maps.google.com/maps?q=<?= rawurlencode($model->checkout_lat) ?>,<?= rawurlencode($model->checkout_long) ?>&z=15&output=embed"
                                    frameborder="0" style="border:0;" allowfullscreen>
                                </iframe>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-clock-slash fs-1 opacity-50 mb-2 d-block"></i>
                            <div class="fw-medium">Pegawai belum melakukan clock out</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
