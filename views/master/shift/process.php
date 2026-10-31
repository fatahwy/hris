<?php

use app\helpers\GeneralHelper;
use kartik\form\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\master\Shift $model */
/** @var yii\widgets\ActiveForm $form */

$isNewRecord = $model->isNewRecord;
if ($isNewRecord) {
    $this->title = 'Tambah Shift';
    $this->params['breadcrumbs'][] = ['label' => 'Shift', 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;
} else {
    $this->title = 'Update Shift: ' . $model->name;
    $this->params['breadcrumbs'][] = ['label' => 'Shift', 'url' => ['index']];
    $this->params['breadcrumbs'][] = ['label' => $model->name];
    $this->params['breadcrumbs'][] = 'Update';
}
?>

<div class="card modern-form-card shadow-sm border-0">
    <div class="card-header bg-white py-3 px-4 border-bottom">
        <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
            <i class="bi bi-clock-history text-primary"></i> <?= Html::encode($this->title) ?>
        </h5>
        <span class="text-muted small">Atur jadwal shift operasional, waktu presensi, dan warna tampilan kalender</span>
    </div>
    <div class="card-body p-4">

        <?php $form = ActiveForm::begin(); ?>

        <div class="row g-3 mb-2">
            <div class="col-12 col-md-8">
                <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => 'Contoh: Shift Pagi, Shift Siang, Regular']) ?>
            </div>
            <div class="col-12 col-md-4">
                <?= $form->field($model, 'color')->textInput(['type' => 'color', 'class' => 'form-control form-control-color w-100 shadow-sm']) ?>
            </div>
        </div>

        <div class="card bg-light border-0 rounded-3 p-3 mb-3">
            <div class="fw-semibold small text-muted mb-2"><i class="bi bi-alarm me-1 text-primary"></i> Pengaturan Waktu Kerja</div>
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <?= $form->field($model, 'checkin_start')->textInput(['type' => 'time'])->label('Mulai Buka Check-in') ?>
                </div>
                <div class="col-12 col-md-4">
                    <?= $form->field($model, 'workhour_start')->textInput(['type' => 'time'])->label('Jam Masuk (Start)') ?>
                </div>
                <div class="col-12 col-md-4">
                    <?= $form->field($model, 'workhour_end')->textInput(['type' => 'time'])->label('Jam Pulang (End)') ?>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12">
                <?= $form->field($model, 'note')->textarea(['rows' => 3, 'placeholder' => 'Catatan tambahan atau keterangan shift...']) ?>
            </div>
        </div>

        <div class="d-flex flex-column-reverse flex-sm-row justify-content-end gap-2 mt-4 pt-3 border-top">
            <?= Html::a('Batal', ['index'], ['class' => 'btn btn-outline-secondary px-4 text-center']) ?>
            <?= Html::submitButton(GeneralHelper::faSave(), ['class' => 'btn btn-primary px-4 shadow-sm text-center']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
