<?php

use app\components\ButtonActionColumn;
use app\helpers\GeneralHelper;
use app\models\master\Company;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\master\search\CompanySearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Perusahaan';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="card modern-grid-card shadow-sm border-0">
    <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom">
        <div>
            <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-buildings text-primary"></i> Data Perusahaan
            </h5>
            <span class="text-muted small">Kelola entitas perusahaan dan limit kuota user</span>
        </div>
        <div>
            <?= Html::a(GeneralHelper::faAdd('Tambah Perusahaan'), ['process'], ['class' => 'btn btn-primary px-3 shadow-sm']) ?>
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
                ['class' => 'yii\grid\SerialColumn'],

                [
                    'attribute' => 'name',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return '<div class="fw-semibold text-dark">' . Html::encode($model->name) . '</div>';
                    }
                ],
                'address',
                [
                    'attribute' => 'status',
                    'format' => 'raw',
                    'value' => function ($m) {
                        return GeneralHelper::textLabel($m->status ? 'Active' : 'Inactive', $m->status);
                    },
                    'contentOptions' => ['class' => 'text-center'],
                    'headerOptions' => ['class' => 'text-center'],
                ],
                [
                    'attribute' => 'max_user',
                    'label' => 'Batas User',
                    'format' => 'raw',
                    'value' => function ($m) {
                        return '<span class="badge bg-light text-dark border px-2.5 py-1.5"><i class="bi bi-people me-1"></i> ' . (int)$m->max_user . ' User</span>';
                    },
                    'contentOptions' => ['class' => 'text-center'],
                    'headerOptions' => ['class' => 'text-center'],
                ],
                [
                    'attribute' => 'payroll_cutoff_date',
                    'label' => 'Cut Off Payroll',
                    'format' => 'raw',
                    'value' => function ($m) {
                        $day = (int)($m->payroll_cutoff_date ?: 20);
                        return '<span class="badge bg-light text-dark border px-2.5 py-1.5"><i class="bi bi-calendar-event me-1 text-primary"></i> Tgl ' . $day . '</span>';
                    },
                    'contentOptions' => ['class' => 'text-center'],
                    'headerOptions' => ['class' => 'text-center'],
                ],
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
                    'class' => ButtonActionColumn::className(),
                    'template' => '{process} {delete}',
                    'urlCreator' => function ($action, Company $model, $key, $index, $column) {
                        return Url::toRoute([$action, 'id' => $model->uuid]);
                    }
                ],
            ],
        ]); ?>
    </div>
</div>
