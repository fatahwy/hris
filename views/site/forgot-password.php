<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */

use kartik\form\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Forgot Password';

// Register the custom CSS
$this->registerCssFile('@web/css/login.css', ['depends' => [\app\assets\AppAsset::class]]);
?>

<div class="login-container">
    <!-- Left Panel: Logo & Branding -->
    <div class="login-left">
        <div class="login-branding">
            <img src="<?= Yii::getAlias('@web/images/login-logo.png') ?>" alt="Pranata HR Logo" class="login-logo"
                id="login-logo">
        </div>
    </div>

    <!-- Right Panel: Form -->
    <div class="login-right">
        <div class="login-form-wrapper">
            <h1 class="login-title">Forgot Password</h1>
            <p class="login-subtitle">Enter your email to reset your password</p>

            <?php if (Yii::$app->session->hasFlash('success')): ?>
                <div class="login-flash">
                    <div class="alert alert-success">
                        <?= Yii::$app->session->getFlash('success') ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (Yii::$app->session->hasFlash('error')): ?>
                <div class="login-flash">
                    <div class="alert alert-danger">
                        <?= Yii::$app->session->getFlash('error') ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php $form = ActiveForm::begin([
                'id' => 'forgot-password-form',
                'options' => ['class' => 'login-form'],
                'fieldConfig' => [
                    'template' => "{label}\n{input}\n{error}",
                    'labelOptions' => ['class' => ''],
                    'inputOptions' => ['class' => 'form-control'],
                    'errorOptions' => ['class' => 'invalid-feedback d-block'],
                ],
            ]); ?>

            <div class="login-form-group">
                <?= Html::label('Email', 'email', ['class' => 'form-label']) ?>
                <?= Html::textInput('email', '', [
                    'autofocus' => true,
                    'placeholder' => 'Enter your email',
                    'id' => 'forgot-email',
                    'class' => 'form-control',
                    'type' => 'email',
                    'required' => true
                ]) ?>
            </div>

            <div class="login-btn-wrapper">
                <?= Html::submitButton('Send Reset Link', [
                    'class' => 'btn-login',
                    'name' => 'forgot-button',
                    'id' => 'forgot-submit-btn',
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>

            <div class="login-bottom">
                <div class="login-register-link">
                    Remember your password?
                    <?= Html::a('Login here', ['/site/login'], ['id' => 'forgot-login-link']) ?>
                </div>
            </div>
        </div>
    </div>
</div>