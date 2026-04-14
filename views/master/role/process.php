<?php

use app\helpers\GeneralHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\master\Client $model */
/** @var yii\widgets\ActiveForm $form */

$isNewRecord = $model->isNewRecord;
if ($isNewRecord) {
    $this->title = 'Create Role';
    $this->params['breadcrumbs'][] = ['label' => 'Roles', 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;
} else {
    $this->title = 'Update Role: ' . $model->label;
    $this->params['breadcrumbs'][] = ['label' => 'Roles', 'url' => ['index']];
    $this->params['breadcrumbs'][] = ['label' => $model->label];
    $this->params['breadcrumbs'][] = 'Update';
}
?>

<div class="client-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'label')->textInput(['maxlength' => true]) ?>

    <div class="form-group text-end">
        <?= Html::submitButton(GeneralHelper::faSave(), ['class' => 'btn btn-primary']) ?>
    </div>


    <?php ActiveForm::end(); ?>

</div>