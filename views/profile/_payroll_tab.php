<?php

use kartik\grid\GridView;
use kartik\daterange\DateRangePicker;
use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $payrollDataProvider */
/** @var app\models\trx\search\PayrollSearch $payrollSearch */
?>

<div class="card modern-grid-card shadow-sm border-0 mt-3">
    <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-wallet2 text-primary"></i> Riwayat Penggajian (Payroll)
            </h6>
            <span class="text-muted small">Histori slip gaji dan rincian penerimaan bulanan Anda</span>
        </div>
    </div>
    <div class="card-body p-0">
        <?= GridView::widget([
            'dataProvider' => $payrollDataProvider,
            'filterModel' => $payrollSearch,
            'responsive' => true,
            'responsiveWrap' => false,
            'containerOptions' => ['style' => 'overflow-x: auto;'],
            'tableOptions' => ['class' => 'table table-hover align-middle mb-0 text-nowrap'],
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
                    'hAlign' => 'right',
                ],
                [
                    'attribute' => 'allowance',
                    'format' => 'raw',
                    'hAlign' => 'right',
                    'value' => function ($model) {
                        if (is_array($model->allowance)) {
                            $total = array_sum(array_column($model->allowance, 'value'));
                            return Yii::$app->formatter->asCurrency($total);
                        }
                        return '-';
                    },
                    'filter' => false,
                ],
                [
                    'attribute' => 'overtime',
                    'format' => 'currency',
                    'filter' => false,
                    'hAlign' => 'right',
                ],
                [
                    'attribute' => 'gross_salary',
                    'format' => 'currency',
                    'filter' => false,
                    'hAlign' => 'right',
                ],
                [
                    'attribute' => 'tax',
                    'format' => 'currency',
                    'filter' => false,
                    'hAlign' => 'right',
                ],
                [
                    'attribute' => 'net_salary',
                    'format' => 'currency',
                    'filter' => false,
                    'hAlign' => 'right',
                    'contentOptions' => ['class' => 'fw-bold text-success'],
                ],
                [
                    'class' => 'kartik\grid\ActionColumn',
                    'header' => 'Aksi',
                    'template' => '{view-slip}',
                    'buttons' => [
                        'view-slip' => function ($url, $model) {
                            return Html::a(
                                '<i class="bi bi-file-earmark-pdf me-1"></i> Slip Gaji',
                                ['/profile/view-payroll', 'id' => $model->uuid],
                                [
                                    'class' => 'btn btn-sm btn-outline-primary shadow-sm px-3',
                                    'target' => '_blank',
                                    'data-pjax' => '0',
                                    'title' => 'Lihat Slip Gaji (PDF)',
                                ]
                            );
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>
</div>
