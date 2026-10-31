<?php

use app\helpers\GeneralHelper;
use kartik\form\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\master\Client $model */
/** @var yii\widgets\ActiveForm $form */

$isNewRecord = $model->isNewRecord;
if ($isNewRecord) {
    $this->title = 'Tambah Client';
    $this->params['breadcrumbs'][] = ['label' => 'Client', 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;
} else {
    $this->title = 'Update Client: ' . $model->name;
    $this->params['breadcrumbs'][] = ['label' => 'Client', 'url' => ['index']];
    $this->params['breadcrumbs'][] = ['label' => $model->name];
    $this->params['breadcrumbs'][] = 'Update';
}
?>

<div class="card modern-form-card shadow-sm border-0">
    <div class="card-header bg-white py-3 px-4 border-bottom">
        <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
            <i class="bi bi-briefcase text-primary"></i> <?= Html::encode($this->title) ?>
        </h5>
        <span class="text-muted small">Lengkapi data klien dan masa aktif langganan di bawah ini</span>
    </div>
    <div class="card-body p-4">

        <?php $form = ActiveForm::begin(); ?>

        <div class="row g-3">
            <div class="col-12 col-md-6">
                <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => 'Nama Klien/Instansi']) ?>
            </div>
            <div class="col-12 col-md-6">
                <?= $form->field($model, 'referensi')->textInput(['maxlength' => true, 'placeholder' => 'Kode / Nomor Referensi']) ?>
            </div>
            <div class="col-12 col-md-6">
                <?= $form->field($model, 'email')->textInput(['type' => 'email', 'placeholder' => 'alamat@email.com']) ?>
            </div>
            <div class="col-12 col-md-6">
                <?= $form->field($model, 'expired_at')->textInput(['type' => 'date']) ?>
            </div>
        </div>

        <div class="d-flex flex-column-reverse flex-sm-row justify-content-end gap-2 mt-4 pt-3 border-top">
            <?= Html::a('Batal', ['index'], ['class' => 'btn btn-outline-secondary px-4 text-center']) ?>
            <?= Html::submitButton(GeneralHelper::faSave(), ['class' => 'btn btn-primary px-4 shadow-sm text-center']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
