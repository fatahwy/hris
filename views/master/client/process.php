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

<div class="card modern-form-card">
    <div class="card-body p-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'expired_at')->textInput(['type' => 'date']) ?>

        <?= $form->field($model, 'email')->textInput(['type' => 'email']) ?>

        <div class="form-group text-end">
            <?= Html::submitButton(GeneralHelper::faSave(), ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>