<?php

use app\models\report\search\AttendanceSummarySearch;
use app\models\trx\Schedule;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\grid\GridView;
use kartik\date\DatePicker;

/** @var AttendanceSummarySearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Laporan Kehadiran Pegawai';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="report-attendance-index">
    <!-- Filter Card -->
    <div class="card modern-form-card mb-4 shadow-sm border-0">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
            <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-funnel text-primary"></i> Filter Periode Laporan
            </h5>
            <span class="text-muted small">Tentukan rentang tanggal analisa kehadiran</span>
        </div>
        <div class="card-body p-4">
            <?php $form = ActiveForm::begin([
                'method' => 'get',
                'options' => ['class' => 'mb-0'],
            ]); ?>

            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-5">
                    <?= $form->field($searchModel, 'date_from')->widget(DatePicker::classname(), [
                        'type' => DatePicker::TYPE_COMPONENT_APPEND,
                        'removeButton' => false,
                        'pluginOptions' => [
                            'autoclose' => true,
                            'format' => 'yyyy-mm-dd',
                        ]
                    ])->label('Tanggal Mulai'); ?>
                </div>

                <div class="col-12 col-md-5">
                    <?= $form->field($searchModel, 'date_to')->widget(DatePicker::classname(), [
                        'type' => DatePicker::TYPE_COMPONENT_APPEND,
                        'removeButton' => false,
                        'pluginOptions' => [
                            'autoclose' => true,
                            'format' => 'yyyy-mm-dd',
                        ]
                    ])->label('Tanggal Selesai'); ?>
                </div>

                <div class="col-12 col-md-2 d-flex gap-2 mb-3">
                    <?= Html::submitButton('<i class="bi bi-filter me-1"></i> Filter', ['class' => 'btn btn-primary px-3 shadow-sm flex-grow-1 text-center']) ?>
                    <?= Html::a('<i class="bi bi-arrow-clockwise"></i>', ['index'], ['class' => 'btn btn-outline-secondary px-3 shadow-sm text-center', 'title' => 'Reset Filter']) ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card modern-grid-card shadow-sm border-0">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-bar-graph text-primary"></i> Rekapitulasi Presensi &amp; Disiplin Kerja
                </h5>
                <span class="text-muted small">Periode: <?= Html::encode($searchModel->date_from) ?> s/d <?= Html::encode($searchModel->date_to) ?></span>
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
                    ['class' => 'kartik\grid\SerialColumn'],

                    [
                        'attribute' => 'name',
                        'label' => 'Nama Pegawai',
                        'format' => 'raw',
                        'vAlign' => 'middle',
                        'value' => function ($m) {
                            return '<div class="fw-semibold text-dark">' . Html::encode($m['name'] ?? '-') . '</div>';
                        }
                    ],
                    [
                        'attribute' => 'jml_shift',
                        'label' => 'Jml Shift',
                        'format' => 'raw',
                        'hAlign' => 'center',
                        'vAlign' => 'middle',
                        'value' => function ($m) use ($searchModel) {
                            return Html::a('<span class="badge bg-light text-dark border px-2 py-1">' . Yii::$app->formatter->asInteger($m['jml_shift']) . '</span>', ['/trx/attendance/index', 'ScheduleSearch[id_user]' => $m['uuid'], 'ScheduleSearch[date]' => $searchModel->date_from . ' - ' . $searchModel->date_to], ['target' => '_blank', 'data-pjax' => 0, 'class' => 'text-decoration-none']);
                        }
                    ],
                    [
                        'attribute' => 'jml_cuti',
                        'label' => 'Jml Cuti',
                        'format' => 'integer',
                        'hAlign' => 'center',
                        'vAlign' => 'middle',
                    ],
                    [
                        'attribute' => 'jml_ijin',
                        'label' => 'Jml Izin',
                        'format' => 'integer',
                        'hAlign' => 'center',
                        'vAlign' => 'middle',
                    ],
                    [
                        'attribute' => 'jml_absen',
                        'label' => 'Jml Absen (Mangkir)',
                        'hAlign' => 'center',
                        'vAlign' => 'middle',
                        'format' => 'raw',
                        'value' => function ($m) use ($searchModel) {
                            $count = (int)$m['jml_absen'];
                            $badgeClass = $count > 0 ? 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25' : 'bg-light text-muted border';
                            return Html::a('<span class="badge ' . $badgeClass . ' px-2 py-1">' . Yii::$app->formatter->asInteger($count) . '</span>', ['/trx/attendance/index', 'ScheduleSearch[id_user]' => $m['uuid'], 'ScheduleSearch[status]' => Schedule::STATUS_ABSENT, 'ScheduleSearch[date]' => $searchModel->date_from . ' - ' . $searchModel->date_to], ['target' => '_blank', 'data-pjax' => 0, 'class' => 'text-decoration-none']);
                        },
                    ],
                    [
                        'attribute' => 'jml_kehadiran',
                        'label' => 'Total Kehadiran',
                        'format' => 'raw',
                        'hAlign' => 'center',
                        'vAlign' => 'middle',
                        'value' => function ($model) {
                            $count = (int)$model['jml_kehadiran'];
                            return '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">' . Yii::$app->formatter->asInteger($count) . '</span>';
                        },
                    ],
                    [
                        'attribute' => 'jml_tepat_waktu',
                        'label' => 'Tepat Waktu',
                        'format' => 'integer',
                        'hAlign' => 'center',
                        'vAlign' => 'middle',
                    ],
                    [
                        'attribute' => 'jml_keterlambatan',
                        'label' => 'Terlambat',
                        'format' => 'raw',
                        'hAlign' => 'center',
                        'vAlign' => 'middle',
                        'value' => function ($model) {
                            $count = (int)$model['jml_keterlambatan'];
                            $badgeClass = $count > 0 ? 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25' : 'bg-light text-muted border';
                            return '<span class="badge ' . $badgeClass . ' px-2 py-1">' . Yii::$app->formatter->asInteger($count) . '</span>';
                        },
                    ],
                    [
                        'attribute' => 'total_jam_kerja',
                        'label' => 'Total Jam Kerja',
                        'format' => ['decimal', 2],
                        'hAlign' => 'center',
                        'vAlign' => 'middle',
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>
