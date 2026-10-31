<?php

use app\helpers\GeneralHelper;
use app\models\AllowanceForm;
use kartik\form\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\master\Company $company */
/** @var AllowanceForm $model */
/** @var string|null $id */

$isNewRecord = $id === null;
if ($isNewRecord) {
    $this->title = 'Tambah Tunjangan';
    $this->params['breadcrumbs'][] = ['label' => 'Tunjangan', 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;
} else {
    $this->title = 'Update Tunjangan: ' . $model->name;
    $this->params['breadcrumbs'][] = ['label' => 'Tunjangan', 'url' => ['index']];
    $this->params['breadcrumbs'][] = ['label' => $model->name];
    $this->params['breadcrumbs'][] = 'Update';
}
?>

<div class="card modern-form-card shadow-sm border-0">
    <div class="card-header bg-white py-3 px-4 border-bottom">
        <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
            <i class="bi bi-wallet2 text-primary"></i> <?= Html::encode($this->title) ?>
        </h5>
        <span class="text-muted small">Atur nama komponen tunjangan karyawan serta status ketetapannya</span>
    </div>
    <div class="card-body p-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'uuid')->hiddenInput()->label(false) ?>

        <div class="row g-3">
            <div class="col-12 col-md-8">
                <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => 'Contoh: Tunjangan Makan, Tunjangan Transport, Keahlian']) ?>
            </div>
            <div class="col-12">
                <div class="form-check form-switch mt-2">
                    <?= Html::activeCheckbox($model, 'is_fixed', [
                        'class' => 'form-check-input',
                        'label' => '<span class="fw-semibold text-dark">Tunjangan Tetap</span> <span class="text-muted small d-block">Centang jika tunjangan ini bernilai tetap dan diberikan setiap periode tanpa terpengaruh kehadiran</span>',
                        'encode' => false,
                    ]) ?>
                </div>
            </div>
        </div>

        <div class="d-flex flex-column-reverse flex-sm-row justify-content-end gap-2 mt-4 pt-3 border-top">
            <?= Html::a('Batal', ['index'], ['class' => 'btn btn-outline-secondary px-4 text-center']) ?>
            <?= Html::submitButton(GeneralHelper::faSave(), ['class' => 'btn btn-primary px-4 shadow-sm text-center']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
