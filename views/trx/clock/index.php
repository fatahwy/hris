<?php

use app\models\trx\Schedule;
use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\daterange\DateRangePicker;

/** @var app\models\trx\search\ScheduleSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Presensi';
$this->params['breadcrumbs'][] = $this->title;

?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'toolbar' => [
        'content' => ($availableSchedule ? Html::a('<i class="bi bi-clock"></i> Clock In', ['clock', 'id' => $availableSchedule->id_schedule, 'type' => 'in'], [
            'class' => 'btn btn-primary',
            'title' => 'Clock In',
            'data-pjax' => '0'
        ]) : '') . ' ' .
            ($activeSchedule ? Html::a('<i class="bi bi-clock"></i> Clock Out', ['clock', 'id' => $activeSchedule->id_schedule, 'type' => 'out'], [
                'class' => 'btn btn-warning',
                'title' => 'Clock Out',
                'data-pjax' => '0'
            ]) : ''),
    ],
    'columns' => [
        ['class' => 'kartik\grid\SerialColumn'],

        [
            'attribute' => 'date',
            'format' => 'date',
            'filter' => DateRangePicker::widget([
                'model' => $searchModel,
                'attribute' => 'date',
                'convertFormat' => true,
                'pluginOptions' => [
                    'locale' => ['format' => 'Y-m-d'],
                    'opens' => 'left',
                ],
                'options' => [
                    'placeholder' => 'Pilih Tanggal',
                    'class' => 'form-control',
                ],
            ]),
        ],
        'shift_name',
        [
            'attribute' => 'checkin_start',
            'format' => 'datetime',
            'filter' => DateRangePicker::widget([
                'model' => $searchModel,
                'attribute' => 'checkin_start',
                'convertFormat' => true,
                'pluginOptions' => [
                    'locale' => ['format' => 'Y-m-d H:i:s'],
                    'opens' => 'left',
                ],
                'options' => [
                    'placeholder' => 'Pilih Tanggal',
                    'class' => 'form-control',
                ],
            ]),
        ],
        [
            'attribute' => 'workhour_end',
            'format' => 'datetime',
            'filter' => DateRangePicker::widget([
                'model' => $searchModel,
                'attribute' => 'workhour_end',
                'convertFormat' => true,
                'pluginOptions' => [
                    'locale' => ['format' => 'Y-m-d H:i:s'],
                    'opens' => 'left',
                ],
                'options' => [
                    'placeholder' => 'Pilih Tanggal',
                    'class' => 'form-control',
                ],
            ]),
        ],
        [
            'attribute' => 'status',
            'format' => 'raw',
            'value' => function ($model) {
                return Schedule::getLabelChip($model);
            },
            'filter' => [
                Schedule::STATUS_SCHEDULED => 'Belum Checkin',
                Schedule::STATUS_CHECKIN => 'Checkin',
                Schedule::STATUS_ABSENT => 'Absent',
                Schedule::STATUS_DONE => 'Selesai',
            ]
        ],
        [
            'attribute' => 'checkin_datetime',
            'format' => 'datetime',
            'filter' => DateRangePicker::widget([
                'model' => $searchModel,
                'attribute' => 'checkin_datetime',
                'convertFormat' => true,
                'pluginOptions' => [
                    'locale' => ['format' => 'Y-m-d H:i:s'],
                    'opens' => 'left',
                ],
                'options' => [
                    'placeholder' => 'Pilih Tanggal',
                    'class' => 'form-control',
                ],
            ]),
        ],
        [
            'attribute' => 'checkout_datetime',
            'format' => 'datetime',
            'filter' => DateRangePicker::widget([
                'model' => $searchModel,
                'attribute' => 'checkout_datetime',
                'convertFormat' => true,
                'pluginOptions' => [
                    'locale' => ['format' => 'Y-m-d H:i:s'],
                    'opens' => 'left',
                ],
                'options' => [
                    'placeholder' => 'Pilih Tanggal',
                    'class' => 'form-control',
                ],
            ]),
        ],
        [
            'class' => 'kartik\grid\ActionColumn',
            'template' => '{view}',
            'buttons' => [
                'view' => function ($url, $model, $key) {
                    return Html::a('<i class="bi bi-eye"></i>', ['view', 'id' => $model->id_schedule], [
                        'class' => 'btn btn-sm btn-outline-primary',
                        'title' => 'View Detail',
                        'data-pjax' => '0'
                    ]);
                },
            ],
        ],
    ],
]); ?>