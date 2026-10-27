<?php

use kartik\grid\GridView;
use kartik\daterange\DateRangePicker;
use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $payrollDataProvider */
/** @var app\models\trx\search\PayrollSearch $payrollSearch */

echo GridView::widget([
    'dataProvider' => $payrollDataProvider,
    'filterModel' => $payrollSearch,
    // 'layout' => '{items}{pager}',
    'responsive' => true,
    'responsiveWrap' => false,
    'containerOptions' => ['style' => 'overflow-x: auto;'],
    'tableOptions' => ['class' => 'table table-striped table-hover text-nowrap'],
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'period_start',
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
            'attribute' => 'period_end',
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
            'attribute' => 'basic_salary',
            'format' => 'currency',
            'filter' => false,
        ],
        [
            'attribute' => 'allowance',
            'format' => 'raw',
            'value' => function ($model) {
                if (is_array($model->allowance)) {
                    $total = array_sum(array_column($model->allowance, 'value'));
                    return Yii::$app->formatter->asInteger($total);
                }
                return '-';
            },
            'filter' => false,
        ],
        [
            'attribute' => 'overtime',
            'format' => 'currency',
            'filter' => false,
        ],
        [
            'attribute' => 'gross_salary',
            'format' => 'currency',
            'filter' => false,
        ],
        [
            'attribute' => 'tax',
            'format' => 'currency',
            'filter' => false,
        ],
        [
            'attribute' => 'net_salary',
            'format' => 'currency',
            'filter' => false,
        ],
        [
            'attribute' => 'created_at',
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
            'header' => 'Aksi',
            'template' => '{view-slip}',
            'buttons' => [
                'view-slip' => function ($url, $model) {
                    return Html::a(
                        '<i class="fas fa-file-invoice-dollar me-1"></i> Slip Gaji',
                        ['/profile/view-payroll', 'id' => $model->uuid],
                        [
                            'class' => 'btn btn-sm btn-outline-primary',
                            'target' => '_blank',
                            'data-pjax' => '0',
                            'title' => 'Lihat Slip Gaji (PDF)',
                        ]
                    );
                },
            ],
        ],
    ],
]);
