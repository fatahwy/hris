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

<div class="card mb-4 shadow-sm border-0 rounded-3">
    <div class="card-header bg-white py-3 border-bottom fw-bold text-dark">
        <i class="bi bi-funnel me-1 text-primary"></i> Filter Periode
    </div>
    <div class="card-body p-3 p-sm-4">
        <?php $form = ActiveForm::begin([
            'method' => 'get',
            'options' => ['class' => 'mb-0'],
        ]); ?>

        <div class="row g-3">
            <div class="col-12 col-md-6">
                <?=
                $form->field($searchModel, 'date_from')->widget(DatePicker::classname(), [
                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                    'removeButton' => false,
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ]
                ])->label('Tanggal Mulai');
                ?>
            </div>

            <div class="col-12 col-md-6">
                <div class="form-group mb-0">
                    <?=
                    $form->field($searchModel, 'date_to')->widget(DatePicker::classname(), [
                        'type' => DatePicker::TYPE_COMPONENT_APPEND,
                        'removeButton' => false,
                        'pluginOptions' => [
                            'autoclose' => true,
                            'format' => 'yyyy-mm-dd',
                        ]
                    ])->label('Tanggal Selesai');
                    ?>
                </div>
            </div>

            <div class="col-12 d-flex flex-column-reverse flex-sm-row justify-content-end gap-2 mt-3">
                <?= Html::a('Reset', ['index'], ['class' => 'btn btn-outline-secondary px-4 text-center']) ?>
                <?= Html::submitButton('Filter', ['class' => 'btn btn-primary px-4 shadow-sm text-center']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'responsive' => true,
    'responsiveWrap' => false,
    'containerOptions' => ['style' => 'overflow-x: auto;'],
    'panel' => [
        'heading' => '<h3 class="panel-title mb-0 fs-6 fw-bold"><i class="bi bi-people me-1"></i> ' . Html::encode($this->title) . '</h3>',
        'type' => 'primary',
        'before' => false,
        'after' => false,
    ],
    'columns' => [
        ['class' => 'kartik\grid\SerialColumn'],

        [
            'attribute' => 'name',
            'label' => 'Nama',
            'vAlign' => 'middle',
        ],
        [
            'attribute' => 'jml_shift',
            'label' => 'Jml Shift',
            'format' => 'raw',
            'value' => function ($m) use ($searchModel) {
                return Html::a(Yii::$app->formatter->asInteger($m['jml_shift']), ['/trx/attendance/index', 'ScheduleSearch[id_user]' => $m['uuid'], 'ScheduleSearch[date]' => $searchModel->date_from . ' - ' . $searchModel->date_to], ['target' => '_blank', 'data-pjax' => 0]);
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
            'format' => 'integer',
            'hAlign' => 'center',
            'vAlign' => 'middle',
        ],
        [
            'attribute' => 'jml_absen',
            'hAlign' => 'center',
            'vAlign' => 'middle',
            'format' => 'raw',
            'value' => function ($m) use ($searchModel) {
                return Html::a(Yii::$app->formatter->asInteger($m['jml_absen']), ['/trx/attendance/index', 'ScheduleSearch[id_user]' => $m['uuid'], 'ScheduleSearch[status]' => Schedule::STATUS_ABSENT, 'ScheduleSearch[date]' => $searchModel->date_from . ' - ' . $searchModel->date_to], ['class' => $m['jml_absen'] > 0 ? 'text-danger fw-bold' : '', 'target' => '_blank', 'data-pjax' => 0]);
            },
        ],
        [
            'attribute' => 'jml_kehadiran',
            'label' => 'Jml Kehadiran',
            'format' => 'integer',
            'hAlign' => 'center',
            'vAlign' => 'middle',
            'contentOptions' => function ($model) {
                return ['class' => $model['jml_kehadiran'] > 0 ? 'text-success fw-bold' : ''];
            },
        ],
        [
            'attribute' => 'jml_tepat_waktu',
            'label' => 'Jml Kehadiran Tepat Waktu',
            'format' => 'integer',
            'hAlign' => 'center',
            'vAlign' => 'middle',
        ],
        [
            'attribute' => 'jml_keterlambatan',
            'label' => 'Jml Keterlambatan',
            'format' => 'integer',
            'hAlign' => 'center',
            'vAlign' => 'middle',
            'contentOptions' => function ($model) {
                return ['class' => $model['jml_keterlambatan'] > 0 ? 'text-warning fw-bold' : ''];
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