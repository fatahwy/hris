<?php

use app\models\trx\Schedule;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var app\models\trx\Schedule $model */

$this->title = 'Detail Kehadiran: ' . $model->user->name . ' - ' . $model->date;
$this->params['breadcrumbs'][] = ['label' => 'Kehadiran', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="schedule-view">
    <!-- <div class="row mb-3">
        <div class="col-md-12 text-end">
            <?= Html::a('<i class="bi bi-arrow-left"></i> Kembali', ['index'], ['class' => 'btn btn-secondary']) ?>
        </div>
    </div> -->

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">Informasi Jadwal</h5>
        </div>
        <div class="card-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'date:date',
                    'shift_name',
                    'checkin_start:datetime',
                    'workhour_end:datetime',
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return Schedule::getLabelChip($model);
                        },
                    ],
                ],
            ]) ?>
        </div>
    </div>

    <div class="row">
        <!-- Clock In Section -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-dark">
                    <h5 class="card-title mb-0"><i class="bi bi-box-arrow-in-right"></i> Clock In</h5>
                </div>
                <div class="card-body">
                    <?php if ($model->checkin_datetime): ?>
                        <div class="mb-3 text-center">
                            <strong>Foto Clock In:</strong><br>
                            <?= Html::img('@web/' . $model->checkin_photo, ['class' => 'img-fluid rounded mt-2 shadow-sm', 'style' => 'max-height: 300px;']) ?>
                        </div>
                        <div class="mb-3">
                            <strong>Waktu:</strong> <?= Yii::$app->formatter->asDatetime($model->checkin_datetime) ?><br>
                            <strong>Lokasi:</strong> <?= $model->checkin_lat ?>, <?= $model->checkin_long ?>
                        </div>
                        <div class="ratio ratio-16x9">
                            <iframe
                                src="https://maps.google.com/maps?q=<?= $model->checkin_lat ?>,<?= $model->checkin_long ?>&z=15&output=embed"
                                frameborder="0" style="border:0;" allowfullscreen>
                            </iframe>
                        </div>
                    <?php else: ?>
                        <p class="text-muted italic">Belum melakukan clock in.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Clock Out Section -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0"><i class="bi bi-box-arrow-right"></i> Clock Out</h5>
                </div>
                <div class="card-body">
                    <?php if ($model->checkout_datetime): ?>
                        <div class="mb-3 text-center">
                            <strong>Foto Clock Out:</strong><br>
                            <?= Html::img('@web/' . $model->checkout_photo, ['class' => 'img-fluid rounded mt-2 shadow-sm', 'style' => 'max-height: 300px;']) ?>
                        </div>
                        <div class="mb-3">
                            <strong>Waktu:</strong> <?= Yii::$app->formatter->asDatetime($model->checkout_datetime) ?><br>
                            <strong>Lokasi:</strong> <?= $model->checkout_lat ?>, <?= $model->checkout_long ?>
                        </div>
                        <div class="ratio ratio-16x9">
                            <iframe
                                src="https://maps.google.com/maps?q=<?= $model->checkout_lat ?>,<?= $model->checkout_long ?>&z=15&output=embed"
                                frameborder="0" style="border:0;" allowfullscreen>
                            </iframe>
                        </div>
                    <?php else: ?>
                        <p class="text-muted italic">Belum melakukan clock out.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>