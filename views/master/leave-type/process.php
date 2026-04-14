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
    $this->title = 'Tambah Izin & Cuti';
    $this->params['breadcrumbs'][] = ['label' => 'Izin & Cuti', 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;
} else {
    $this->title = 'Update Izin & Cuti: ' . $model->name;
    $this->params['breadcrumbs'][] = ['label' => 'Izin & Cuti', 'url' => ['index']];
    $this->params['breadcrumbs'][] = ['label' => $model->name];
    $this->params['breadcrumbs'][] = 'Update';
}
?>

<div class="card modern-form-card">
    <div class="card-body p-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'category')->dropDownList(LeaveType::optsCategory(), ['disabled' => !$isNewRecord]) ?>

        <?= $form->field($model, 'max_day')->textInput() ?>

        <div class="form-group text-end">
            <?= Html::submitButton(GeneralHelper::faSave(), ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>