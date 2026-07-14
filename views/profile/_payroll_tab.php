<?php

use kartik\grid\GridView;
use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $payrollDataProvider */
/** @var app\models\trx\search\PayrollSearch $payrollSearch */

echo GridView::widget([
    'dataProvider' => $payrollDataProvider,
    'filterModel' => $payrollSearch,
    'layout' => '{items}{pager}',
    'tableOptions' => ['class' => 'table table-striped table-hover'],
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'period_start:date',
        'period_end:date',
        'basic_salary:currency',
        [
            'attribute' => 'allowance',
            'format' => 'raw',
            'value' => function ($model) {
                if (is_array($model->allowance)) {
                    $total = array_sum(array_column($model->allowance, 'value'));
                    return number_format($total, 0, ',', '.');
                }
                return '-';
            }
        ],
        'overtime:currency',
        'dedection:currency',
        'tax:currency',
        'gross_salary:currency',
        'net_salary:currency',
        'created_at:datetime',
    ],
]) ?>
