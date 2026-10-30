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

<div class="card modern-form-card">
    <div class="card-body p-3 p-sm-4">

        <?php $form = ActiveForm::begin(); ?>

        <div class="row">
            <div class="col-12 col-md-8">
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-12 col-md-4">
                <?= $form->field($model, 'color')->textInput(['type' => 'color', 'class' => 'form-control form-control-color w-100']) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-md-4">
                <?= $form->field($model, 'checkin_start')->textInput(['type' => 'time']) ?>
            </div>
            <div class="col-12 col-md-4">
                <?= $form->field($model, 'workhour_start')->textInput(['type' => 'time']) ?>
            </div>
            <div class="col-12 col-md-4">
                <?= $form->field($model, 'workhour_end')->textInput(['type' => 'time']) ?>
            </div>
        </div>

        <?= $form->field($model, 'note')->textarea(['rows' => 4]) ?>

        <div class="form-group d-flex justify-content-end mt-4">
            <?= Html::submitButton(GeneralHelper::faSave(), ['class' => 'btn btn-primary px-4 shadow-sm w-100 w-sm-auto']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>