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

<div class="card modern-form-card">
    <div class="card-body p-3 p-sm-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'uuid')->hiddenInput()->label(false) ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'is_fixed')->checkbox() ?>

        <div class="form-group d-flex justify-content-end mt-4">
            <?= Html::submitButton(GeneralHelper::faSave(), ['class' => 'btn btn-primary px-4 shadow-sm w-100 w-sm-auto']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
