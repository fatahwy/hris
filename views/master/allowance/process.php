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
    <div class="card-body p-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'uuid')->hiddenInput()->label(false) ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'is_fixed')->checkbox() ?>

        <div class="form-group text-end">
            <?= Html::submitButton(GeneralHelper::faSave(), ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
