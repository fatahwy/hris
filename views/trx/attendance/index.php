<?php

use app\models\trx\Schedule;
use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\daterange\DateRangePicker;

/** @var app\models\trx\search\ScheduleSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Kehadiran';
$this->params['breadcrumbs'][] = $this->title;

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    // 'toolbar' => [
    //     'content' => ($availableSchedule ? Html::a('<i class="bi bi-clock"></i> Clock In', ['clock', 'id' => $availableSchedule->id_schedule, 'type' => 'in'], [
    //         'class' => 'btn btn-primary',
    //         'title' => 'Clock In',
    //         'data-pjax' => '0'
    //     ]) : '') . ' ' .
    //         ($activeSchedule ? Html::a('<i class="bi bi-clock"></i> Clock Out', ['clock', 'id' => $activeSchedule->id_schedule, 'type' => 'out'], [
    //             'class' => 'btn btn-warning',
    //             'title' => 'Clock Out',
    //             'data-pjax' => '0'
    //         ]) : ''),
    // ],
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
        ],
        'shift_name',
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
                        'title' => 'View Detail',
                        'data-pjax' => '0'
                    ]);
                },
            ],
        ],
    ],
]);
