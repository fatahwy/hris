<?php

use app\models\trx\Schedule;
use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\daterange\DateRangePicker;

/** @var app\models\trx\search\ScheduleSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Kehadiran';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="card modern-grid-card shadow-sm border-0">
    <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom">
        <div>
            <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-calendar-check text-primary"></i> Data Presensi &amp; Kehadiran
            </h5>
            <span class="text-muted small">Riwayat check-in, check-out, jadwal shift, dan status kehadiran karyawan</span>
        </div>
    </div>
    <div class="card-body p-0">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'responsive' => true,
            'responsiveWrap' => false,
            'tableOptions' => ['class' => 'table table-hover align-middle mb-0 text-nowrap'],
            'containerOptions' => ['style' => 'overflow-x: auto;'],
            'columns' => [
                ['class' => 'kartik\grid\SerialColumn'],

                [
                    'attribute' => 'date',
                    'format' => 'date',
                    'filterType' => GridView::FILTER_DATE_RANGE,
                    'filterWidgetOptions' => [
                        'convertFormat' => true,
                        'pluginOptions' => [
                            'locale' => [
                                'format' => 'Y-m-d',
                            ],
                            'opens' => 'left',
                        ],
                        'options' => [
                            'placeholder' => 'Pilih Tanggal',
                        ],
                    ],
                ],
                [
                    'attribute' => 'id_user',
                    'value' => 'user.name',
                    'visible' => empty($hideUser),
                    'format' => 'raw',
                    'value' => function ($m) {
                        return '<div class="fw-semibold text-dark">' . Html::encode($m->user->name ?? '-') . '</div>';
                    }
                ],
                [
                    'attribute' => 'shift_name',
                    'format' => 'raw',
                    'value' => function ($m) {
                        return '<span class="badge bg-light text-dark border px-2.5 py-1.5"><i class="bi bi-clock me-1 text-primary"></i>' . Html::encode($m->shift_name) . '</span>';
                    }
                ],
                [
                    'attribute' => 'checkin_start',
                    'format' => 'datetime',
                    'filterType' => GridView::FILTER_DATE_RANGE,
                    'filterWidgetOptions' => [
                        'convertFormat' => true,
                        'pluginOptions' => [
                            'locale' => [
                                'format' => 'Y-m-d',
                            ],
                            'opens' => 'left',
                        ],
                        'options' => [
                            'placeholder' => 'Pilih Tanggal',
                        ],
                    ],
                ],
                [
                    'attribute' => 'workhour_end',
                    'format' => 'datetime',
                    'filterType' => GridView::FILTER_DATE_RANGE,
                    'filterWidgetOptions' => [
                        'convertFormat' => true,
                        'pluginOptions' => [
                            'locale' => [
                                'format' => 'Y-m-d',
                            ],
                            'opens' => 'left',
                        ],
                        'options' => [
                            'placeholder' => 'Pilih Tanggal',
                        ],
                    ],
                ],
                [
                    'attribute' => 'status',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return Schedule::getLabelChip($model);
                    },
                    'filter' => [
                        Schedule::STATUS_SCHEDULED => 'Scheduled',
                        Schedule::STATUS_CHECKIN => 'Checkin',
                        Schedule::STATUS_ABSENT => 'Absent',
                        Schedule::STATUS_DONE => 'Selesai',
                    ],
                    'filterType' => GridView::FILTER_SELECT2,
                    'filterWidgetOptions' => [
                        'options' => ['prompt' => 'Pilih'],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'width' => '100px'
                        ],
                    ],
                ],
                [
                    'attribute' => 'checkin_datetime',
                    'format' => 'datetime',
                    'filterType' => GridView::FILTER_DATE_RANGE,
                    'filterWidgetOptions' => [
                        'convertFormat' => true,
                        'pluginOptions' => [
                            'locale' => [
                                'format' => 'Y-m-d',
                            ],
                            'opens' => 'left',
                        ],
                        'options' => [
                            'placeholder' => 'Pilih Tanggal',
                        ],
                    ],
                ],
                [
                    'attribute' => 'checkout_datetime',
                    'format' => 'datetime',
                    'filterType' => GridView::FILTER_DATE_RANGE,
                    'filterWidgetOptions' => [
                        'convertFormat' => true,
                        'pluginOptions' => [
                            'locale' => [
                                'format' => 'Y-m-d',
                            ],
                            'opens' => 'left',
                        ],
                        'options' => [
                            'placeholder' => 'Pilih Tanggal',
                        ],
                    ],
                ],
                [
                    'class' => 'kartik\grid\ActionColumn',
                    'template' => '{view}',
                    'buttons' => [
                        'view' => function ($url, $model, $key) {
                            return Html::a('<i class="bi bi-eye"></i>', ['view', 'id' => $model->id_schedule], [
                                'class' => 'btn btn-sm btn-outline-primary',
                                'title' => 'Lihat Detail Presensi',
                                'data-pjax' => '0'
                            ]);
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>
</div>
