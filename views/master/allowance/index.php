<?php

use app\components\ButtonActionColumn;
use app\helpers\GeneralHelper;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\master\Company $company */
/** @var array $allowances */

$this->title = 'Tunjangan';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="card modern-grid-card shadow-sm border-0">
    <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom">
        <div>
            <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-wallet2 text-primary"></i> Data Komponen Tunjangan
            </h5>
            <span class="text-muted small">Kelola tunjangan tetap dan tidak tetap untuk perhitungan payroll</span>
        </div>
        <div>
            <?= Html::a(GeneralHelper::faAdd('Tambah Tunjangan'), ['process'], ['class' => 'btn btn-primary px-3 shadow-sm']) ?>
        </div>
    </div>
    <div class="card-body p-0">
        <?= GridView::widget([
            'dataProvider' => new \yii\data\ArrayDataProvider([
                'allModels' => $allowances,
                'pagination' => false,
            ]),
            'responsive' => true,
            'responsiveWrap' => false,
            'tableOptions' => ['class' => 'table table-hover align-middle mb-0 text-nowrap'],
            'containerOptions' => ['style' => 'overflow-x: auto;'],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'name',
                    'label' => 'Nama Tunjangan',
                    'format' => 'raw',
                    'value' => function ($m) {
                        return '<div class="fw-semibold text-dark"><i class="bi bi-cash me-2 text-success"></i>' . Html::encode($m['name']) . '</div>';
                    }
                ],
                [
                    'label' => 'Jenis Tunjangan',
                    'attribute' => 'is_fixed',
                    'format' => 'raw',
                    'value' => function ($m) {
                        return !empty($m['is_fixed']) 
                            ? '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1.5"><i class="bi bi-check-circle me-1"></i>Tunjangan Tetap</span>' 
                            : '<span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2.5 py-1.5"><i class="bi bi-dash-circle me-1"></i>Tunjangan Tidak Tetap</span>';
                    },
                ],
                [
                    'class' => ButtonActionColumn::className(),
                    'template' => '{process} {delete}',
                    'urlCreator' => function ($action, $model, $key, $index, $column) {
                        return Url::toRoute([$action, 'id' => $model['uuid']]);
                    }
                ],
            ],
        ]); ?>
    </div>
</div>
