<?php

use app\helpers\GeneralHelper;
use kartik\form\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\master\Client $model */
/** @var yii\widgets\ActiveForm $form */

$isNewRecord = $model->isNewRecord;
if ($isNewRecord) {
    $this->title = 'Create Client';
    $this->params['breadcrumbs'][] = ['label' => 'Clients', 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;
} else {
    $this->title = 'Update Client: ' . $model->name;
    $this->params['breadcrumbs'][] = ['label' => 'Clients', 'url' => ['index']];
    $this->params['breadcrumbs'][] = ['label' => $model->name];
    $this->params['breadcrumbs'][] = 'Update';
}
?>

<div class="card modern-form-card">
    <div class="card-body p-4">

        <?php $form = ActiveForm::begin([
            'id' => 'client-form',
            'layout' => 'horizontal',
            'fieldConfig' => [
                'horizontalCssClasses' => [
                    'label' => 'col-sm-3 col-form-label',
                    'offset' => 'offset-sm-3',
                    'wrapper' => 'col-sm-9',
                ],
            ],
        ]); ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'expired_at')->textInput(['type' => 'date']) ?>

        <?= $form->field($model, 'email')->textInput(['type' => 'email']) ?>

        <div class="form-group text-end">
            <?= Html::submitButton(GeneralHelper::faSave(), ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>