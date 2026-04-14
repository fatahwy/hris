<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\master\search\ShiftSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="shift-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id_shift') ?>

    <?= $form->field($model, 'id_company') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'checkin_start') ?>

    <?= $form->field($model, 'workhour_start') ?>

    <?php // echo $form->field($model, 'workhour_end') ?>

    <?php // echo $form->field($model, 'color') ?>

    <?php // echo $form->field($model, 'note') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
