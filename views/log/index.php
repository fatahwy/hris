<?php

use app\helpers\GeneralHelper;
use kartik\date\DatePicker;
use kartik\form\ActiveForm;
use kartik\grid\GridView;
use yii\bootstrap5\Html;
use yii\data\ActiveDataProvider;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $dataProvider ActiveDataProvider */

$this->title = 'Log Aktivitas';
$this->params['breadcrumbs'][] = $this->title;

$form = ActiveForm::begin(['action' => Url::toRoute('index'), 'method' => 'GET']);
?>

<div class="log-index">
    <!-- Filter Card -->
    <div class="card modern-form-card mb-4 shadow-sm border-0">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
            <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-funnel text-primary"></i> Filter Rentang Log
            </h5>
            <span class="text-muted small">Cari riwayat jejak audit aktivitas sistem</span>
        </div>
        <div class="card-body p-4">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                    <?= $form->field($filter, 'date_start')->widget(DatePicker::classname(), [
                        'options' => ['placeholder' => 'Tanggal Mulai'],
                        'removeButton' => false,
                        'pluginOptions' => [
                            'autoclose' => true,
                            'format' => 'yyyy-mm-dd'
                        ]
                    ])->label('Tanggal Mulai') ?>
                </div>
                <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                    <?= $form->field($filter, 'date_end')->widget(DatePicker::classname(), [
                        'options' => ['placeholder' => 'Tanggal Selesai'],
                        'removeButton' => false,
                        'pluginOptions' => [
                            'autoclose' => true,
                            'format' => 'yyyy-mm-dd'
                        ]
                    ])->label('Tanggal Selesai') ?>
                </div>
                <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                    <div class="form-group mb-3 d-flex gap-2">
                        <?= Html::submitButton(GeneralHelper::faSearch() . ' Cari Log', ['class' => 'btn btn-primary px-4 shadow-sm flex-grow-1']) ?>
                        <?= Html::a('<i class="bi bi-arrow-clockwise"></i>', ['index'], ['class' => 'btn btn-outline-secondary px-3 shadow-sm', 'title' => 'Reset']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php $form->end(); ?>

    <!-- Table Card -->
    <div class="card modern-grid-card shadow-sm border-0">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-journal-code text-primary"></i> Daftar Audit Log
                </h5>
                <span class="text-muted small">Catatan histori aksi user, endpoint URL, dan data payload</span>
            </div>
        </div>
        <div class="card-body p-0">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $model,
                'responsive' => true,
                'responsiveWrap' => false,
                'tableOptions' => ['class' => 'table table-hover align-middle mb-0 text-nowrap'],
                'containerOptions' => ['style' => 'overflow-x: auto;'],
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute' => 'created_at',
                        'format' => 'datetime',
                        'contentOptions' => ['class' => 'text-muted small'],
                        'filterType' => GridView::FILTER_DATE,
                        'filterWidgetOptions' => [
                            'options' => ['prompt' => 'Pilih'],
                            'pluginOptions' => [
                                'autoclose' => true,
                                'format' => 'yyyy-mm-dd'
                            ],
                        ],
                    ],
                    [
                        'label' => 'Username',
                        'attribute' => 'id_user',
                        'format' => 'raw',
                        'value' => function ($m) {
                            return '<div class="fw-semibold text-dark"><i class="bi bi-person text-primary me-1"></i>' . Html::encode($m->user->username ?? '-') . '</div>';
                        }
                    ],
                    [
                        'attribute' => 'url',
                        'format' => 'raw',
                        'value' => function ($m) {
                            return '<code class="small text-secondary">' . Html::encode($m->url) . '</code>';
                        }
                    ],
                    [
                        'attribute' => 'data',
                        'label' => 'Payload Data',
                        'format' => 'raw',
                        'value' => function ($m) {
                            return Html::tag('div', '<pre class="bg-light p-2 rounded small m-0 font-monospace text-muted" style="max-width:400px; max-height:80px; overflow:hidden; font-size: 0.75rem;">' . Html::encode(StringHelper::truncateWords($m->data, 15)) . '</pre>');
                        }
                    ],
                    [
                        'class' => 'app\components\ButtonActionColumn',
                        'template' => '{view}'
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>
