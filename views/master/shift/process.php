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
    <div class="card-body p-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'checkin_start')->textInput(['type' => 'time']) ?>

        <?= $form->field($model, 'workhour_start')->textInput(['type' => 'time']) ?>

        <?= $form->field($model, 'workhour_end')->textInput(['type' => 'time']) ?>

        <?= $form->field($model, 'color')->textInput(['type' => 'color']) ?>

        <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>

        <div class="form-group text-end">
            <?= Html::submitButton(GeneralHelper::faSave(), ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>