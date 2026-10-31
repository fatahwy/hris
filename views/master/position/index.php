<?php

use app\components\ButtonActionColumn;
use app\helpers\GeneralHelper;
use app\models\master\Position;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\master\search\PositionSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Jabatan';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="card modern-grid-card shadow-sm border-0">
    <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom">
        <div>
            <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-person-badge text-primary"></i> Data Jabatan
            </h5>
            <span class="text-muted small">Kelola tingkatan dan nama jabatan pegawai</span>
        </div>
        <div>
            <?= Html::a(GeneralHelper::faAdd('Tambah Jabatan'), ['process'], ['class' => 'btn btn-primary px-3 shadow-sm']) ?>
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
                        return '<div class="fw-semibold text-dark"><i class="bi bi-award text-muted me-2"></i>' . Html::encode($model->name) . '</div>';
                    }
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
                    'urlCreator' => function ($action, Position $model, $key, $index, $column) {
                        return Url::toRoute([$action, 'id' => $model->uuid]);
                    }
                ],
            ],
        ]); ?>
    </div>
</div>
