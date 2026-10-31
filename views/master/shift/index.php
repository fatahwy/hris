<?php

use app\components\ButtonActionColumn;
use app\helpers\GeneralHelper;
use app\models\master\Shift;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\master\search\ShiftSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Shift';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="card modern-grid-card shadow-sm border-0">
    <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom">
        <div>
            <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-clock-history text-primary"></i> Data Shift Kerja
            </h5>
            <span class="text-muted small">Kelola jam masuk, jam pulang, warna label, dan toleransi check-in</span>
        </div>
        <div>
            <?= Html::a(GeneralHelper::faAdd('Tambah Shift'), ['process'], ['class' => 'btn btn-primary px-3 shadow-sm']) ?>
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
                    'value' => function ($m) {
                        return '<div class="fw-semibold text-dark">' . Html::encode($m->name) . '</div>';
                    }
                ],
                [
                    'attribute' => 'color',
                    'label' => 'Label Warna',
                    'format' => 'raw',
                    'value' => function ($m) {
                        $color = $m->color ?: '#2b5fe1';
                        return '<div class="d-inline-flex align-items-center gap-2 px-2.5 py-1 rounded-pill border bg-light small">' .
                               '<span class="rounded-circle shadow-sm" style="display:inline-block; width:14px; height:14px; background-color: ' . Html::encode($color) . ';"></span>' .
                               '<code class="text-secondary small">' . Html::encode($color) . '</code>' .
                               '</div>';
                    },
                    'filter' => false,
                ],
                [
                    'attribute' => 'checkin_start',
                    'label' => 'Mulai Check-in',
                    'format' => 'raw',
                    'value' => function ($m) {
                        return '<span class="badge bg-light text-secondary border font-monospace px-2 py-1"><i class="bi bi-box-arrow-in-right me-1 text-primary"></i>' . Html::encode(substr($m->checkin_start, 0, 5)) . '</span>';
                    }
                ],
                [
                    'attribute' => 'workhour_start',
                    'label' => 'Jam Masuk',
                    'format' => 'raw',
                    'value' => function ($m) {
                        return '<span class="badge bg-light text-dark border font-monospace px-2 py-1"><i class="bi bi-clock me-1 text-success"></i>' . Html::encode(substr($m->workhour_start, 0, 5)) . '</span>';
                    }
                ],
                [
                    'attribute' => 'workhour_end',
                    'label' => 'Jam Pulang',
                    'format' => 'raw',
                    'value' => function ($m) {
                        return '<span class="badge bg-light text-dark border font-monospace px-2 py-1"><i class="bi bi-box-arrow-right me-1 text-danger"></i>' . Html::encode(substr($m->workhour_end, 0, 5)) . '</span>';
                    }
                ],
                [
                    'attribute' => 'note',
                    'label' => 'Catatan',
                    'value' => function ($m) {
                        return !empty($m->note) ? $m->note : '-';
                    }
                ],
                [
                    'class' => ButtonActionColumn::className(),
                    'template' => '{process} {delete}',
                    'urlCreator' => function ($action, Shift $model, $key, $index, $column) {
                        return Url::toRoute([$action, 'id' => $model->uuid]);
                    }
                ],
            ],
        ]); ?>
    </div>
</div>
