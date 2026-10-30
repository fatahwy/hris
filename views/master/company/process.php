<?php

use app\helpers\GeneralHelper;
use kartik\form\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\master\Company $model */
/** @var yii\widgets\ActiveForm $form */

$isNewRecord = $model->isNewRecord;
if ($isNewRecord) {
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

<div class="card modern-form-card">
    <div class="card-body p-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'status')->dropDownList([1 => 'Active', 0 => 'Inactive'], ['class' => 'form-select w-100 w-sm-50']) ?>

        <?= $form->field($model, 'max_user')->textInput(['type' => 'number']) ?>

        <div class="form-group d-flex justify-content-end mt-4">
            <?= Html::submitButton(GeneralHelper::faSave(), ['class' => 'btn btn-primary px-4 shadow-sm']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>