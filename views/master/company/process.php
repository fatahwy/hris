<?php

use app\helpers\GeneralHelper;
use kartik\form\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\master\Company $model */
/** @var yii\widgets\ActiveForm $form */

$isNewRecord = $model->isNewRecord;
if ($isNewRecord) {
    if (empty($model->payroll_cutoff_date)) {
        $model->payroll_cutoff_date = 20;
    }
    $this->title = 'Tambah Perusahaan';
    $this->params['breadcrumbs'][] = ['label' => 'Perusahaan', 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;
} else {
    $this->title = 'Update Perusahaan: ' . $model->name;
    $this->params['breadcrumbs'][] = ['label' => 'Perusahaan', 'url' => ['index']];
    $this->params['breadcrumbs'][] = ['label' => $model->name];
    $this->params['breadcrumbs'][] = 'Update';
}
?>

<div class="card modern-form-card shadow-sm border-0">
    <div class="card-header bg-white py-3 px-4 border-bottom">
        <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
            <i class="bi bi-buildings text-primary"></i> <?= Html::encode($this->title) ?>
        </h5>
        <span class="text-muted small">Atur profil perusahaan, alamat, status, dan kuota maksimum akun karyawan</span>
    </div>
    <div class="card-body p-4">

        <?php $form = ActiveForm::begin(); ?>

        <div class="row g-3">
            <div class="col-12 col-md-8">
                <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => 'Nama Perusahaan / PT / CV']) ?>
            </div>
            <div class="col-12 col-md-4">
                <?= $form->field($model, 'status')->dropDownList([1 => 'Active', 0 => 'Inactive'], ['class' => 'form-select']) ?>
            </div>
            <div class="col-12">
                <?= $form->field($model, 'address')->textInput(['maxlength' => true, 'placeholder' => 'Alamat lengkap operasional perusahaan']) ?>
            </div>
            <div class="col-12 col-md-6">
                <?= $form->field($model, 'max_user')->textInput(['type' => 'number', 'placeholder' => 'Jumlah maksimum karyawan']) ?>
            </div>
            <div class="col-12 col-md-6">
                <?= $form->field($model, 'payroll_cutoff_date')->textInput([
                    'type' => 'number',
                    'min' => 1,
                    'max' => 28,
                    'placeholder' => '20',
                ])->hint('Tanggal cut off payroll bulanan (1 - 28, default: 20). Periode dihitung dari tanggal setelah cut off bulan lalu s/d tanggal cut off bulan ini.') ?>
            </div>
        </div>

        <div class="d-flex flex-column-reverse flex-sm-row justify-content-end gap-2 mt-4 pt-3 border-top">
            <?= Html::a('Batal', ['index'], ['class' => 'btn btn-outline-secondary px-4 text-center']) ?>
            <?= Html::submitButton(GeneralHelper::faSave(), ['class' => 'btn btn-primary px-4 shadow-sm text-center']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>