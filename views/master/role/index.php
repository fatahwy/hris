<?php

use app\components\ButtonActionColumn;
use app\helpers\GeneralHelper;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\master\search\ClientSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Role';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="card modern-grid-card shadow-sm border-0">
    <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom">
        <div>
            <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-shield-lock text-primary"></i> Data Role &amp; Peran
            </h5>
            <span class="text-muted small">Kelola penamaan peran otorisasi user di aplikasi</span>
        </div>
        <div>
            <?= Html::a(GeneralHelper::faAdd('Tambah Role'), ['process'], ['class' => 'btn btn-primary px-3 shadow-sm']) ?>
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
                    'attribute' => 'label',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return '<div class="fw-semibold text-dark"><i class="bi bi-shield-check text-primary me-2"></i>' . Html::encode($model->label) . '</div>';
                    }
                ],
                [
                    'class' => ButtonActionColumn::className(),
                    'template' => '{process} {delete}',
                    'urlCreator' => function ($action, $model, $key, $index, $column) {
                        return Url::toRoute([$action, 'id' => $model->name]);
                    },
                    'visibleButtons' => [
                        'process' => function ($model) {
                            return !in_array(strtolower($model->name), ['super', 'owner']);
                        },
                        'delete' => function ($model) {
                            return !in_array(strtolower($model->name), ['super', 'owner']);
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>
</div>
