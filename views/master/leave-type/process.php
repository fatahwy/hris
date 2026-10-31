<?php

use app\helpers\GeneralHelper;
use app\models\master\LeaveType;
use kartik\form\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\master\LeaveType $model */
/** @var yii\widgets\ActiveForm $form */

$isNewRecord = $model->isNewRecord;
if ($isNewRecord) {
    $this->title = 'Tambah Tipe Cuti / Izin';
    $this->params['breadcrumbs'][] = ['label' => 'Izin & Cuti', 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;
} else {
    $this->title = 'Update Cuti: ' . $model->name;
    $this->params['breadcrumbs'][] = ['label' => 'Izin & Cuti', 'url' => ['index']];
    $this->params['breadcrumbs'][] = ['label' => $model->name];
    $this->params['breadcrumbs'][] = 'Update';
}
?>

<div class="card modern-form-card shadow-sm border-0">
    <div class="card-header bg-white py-3 px-4 border-bottom">
        <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
            <i class="bi bi-calendar-event text-primary"></i> <?= Html::encode($this->title) ?>
        </h5>
        <span class="text-muted small">Atur nama jenis izin/cuti dan batas kuota maksimal penggunaan hari</span>
    </div>
    <div class="card-body p-4">

        <?php $form = ActiveForm::begin(); ?>

        <div class="row g-3">
            <div class="col-12 col-md-7">
                <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => 'Contoh: Cuti Melahirkan, Izin Sakit, Cuti Menikah']) ?>
            </div>
            <div class="col-12 col-md-5">
                <?= $form->field($model, 'max_day')->textInput(['type' => 'number', 'placeholder' => 'Jumlah hari (kosongkan jika tanpa batas)'])->label('Maksimal Hari (Kuota)') ?>
            </div>
        </div>

        <div class="d-flex flex-column-reverse flex-sm-row justify-content-end gap-2 mt-4 pt-3 border-top">
            <?= Html::a('Batal', ['index'], ['class' => 'btn btn-outline-secondary px-4 text-center']) ?>
            <?= Html::submitButton(GeneralHelper::faSave(), ['class' => 'btn btn-primary px-4 shadow-sm text-center']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
