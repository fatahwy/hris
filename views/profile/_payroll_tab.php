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
    'tableOptions' => ['class' => 'table table-striped table-hover'],
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
    ],
]);
