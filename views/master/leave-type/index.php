<?php

use app\components\ButtonActionColumn;
use app\helpers\GeneralHelper;
use app\models\master\LeaveType;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\master\search\LeaveTypeSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Izin & Cuti';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="card modern-grid-card shadow-sm border-0">
    <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom">
        <div>
            <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-calendar-event text-primary"></i> Data Izin &amp; Cuti
            </h5>
            <span class="text-muted small">Kelola tipe perizinan, kuota maksimal cuti, dan kategorinya</span>
        </div>
        <div>
            <?= Html::a(GeneralHelper::faAdd('Tambah Tipe Cuti/Izin'), ['process'], ['class' => 'btn btn-primary px-3 shadow-sm']) ?>
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
                    'attribute' => 'category',
                    'format' => 'raw',
                    'value' => function ($m) {
                        $isLeave = ($m->category == LeaveType::CATEGORY_LEAVE);
                        $badgeClass = $isLeave ? 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25' : 'bg-info bg-opacity-10 text-info border border-info border-opacity-25';
                        $icon = $isLeave ? 'bi-calendar-check' : 'bi-clock';
                        return '<span class="badge ' . $badgeClass . ' px-2.5 py-1.5"><i class="bi ' . $icon . ' me-1"></i>' . Html::encode(LeaveType::optsCategory()[$m->category] ?? $m->category) . '</span>';
                    },
                    'filter' => LeaveType::optsCategory(),
                ],
                [
                    'attribute' => 'max_day',
                    'label' => 'Maksimal Hari',
                    'format' => 'raw',
                    'value' => function ($m) {
                        if ($m->max_day === null) {
                            return '<span class="text-muted small">Tidak dibatasi</span>';
                        }
                        return '<span class="badge bg-light text-dark border px-2.5 py-1.5"><i class="bi bi-hourglass-split me-1 text-warning"></i> ' . (int)$m->max_day . ' Hari</span>';
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
                    'urlCreator' => function ($action, LeaveType $model, $key, $index, $column) {
                        return Url::toRoute([$action, 'id' => $model->uuid]);
                    },
                    'visibleButtons' => [
                        'process' => function ($model) {
                            return !in_array(intval($model->id_leave_type), LeaveType::RES_ID);
                        },
                        'delete' => function ($model) {
                            return !in_array(intval($model->id_leave_type), LeaveType::RES_ID);
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>
</div>
