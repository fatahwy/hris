<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */

use kartik\form\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'New Password';

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
            <h1 class="login-title">New Password</h1>
            <p class="login-subtitle">Create a new password for your account</p>

            <?php if (Yii::$app->session->hasFlash('error')): ?>
                <div class="login-flash">
                    <div class="alert alert-danger">
                        <?= Yii::$app->session->getFlash('error') ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php $form = ActiveForm::begin([
                'id' => 'new-password-form',
                'options' => ['class' => 'login-form'],
                'fieldConfig' => [
                    'template' => "{label}\n{input}\n{error}",
                    'labelOptions' => ['class' => ''],
                    'inputOptions' => ['class' => 'form-control'],
                    'errorOptions' => ['class' => 'invalid-feedback d-block'],
                ],
            ]); ?>

            <div class="login-form-group">
                <?= Html::label('New Password', 'password', ['class' => 'form-label']) ?>
                <div class="position-relative">
                    <?= Html::passwordInput('password', '', [
                        'placeholder' => 'Enter new password',
                        'id' => 'new-password',
                        'class' => 'form-control',
                        'style' => 'padding-right: 45px;',
                        'required' => true,
                        'minlength' => 6
                    ]) ?>
                    <span class="toggle-password position-absolute" data-target="new-password"
                        style="right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #b0aec8; z-index: 10;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                            class="eye-icon" viewBox="0 0 16 16">
                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z" />
                            <path
                                d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z" />
                        </svg>
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                            class="eye-slash-icon d-none" viewBox="0 0 16 16">
                            <path
                                d="m10.79 12.912-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7.029 7.029 0 0 0 2.79-.588zM5.21 3.088A7.028 7.028 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474L5.21 3.089z" />
                            <path
                                d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829l-2.83-2.829zm4.95.708-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829zm3.171 6-12-12 .708-.708 12 12-.708.708z" />
                        </svg>
                    </span>
                </div>
            </div>

            <div class="login-form-group">
                <?= Html::label('Confirm Password', 'confirm_password', ['class' => 'form-label']) ?>
                <div class="position-relative">
                    <?= Html::passwordInput('confirm_password', '', [
                        'placeholder' => 'Repeat new password',
                        'id' => 'confirm-password',
                        'class' => 'form-control',
                        'style' => 'padding-right: 45px;',
                        'required' => true,
                        'minlength' => 6
                    ]) ?>
                    <span class="toggle-password position-absolute" data-target="confirm-password"
                        style="right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #b0aec8; z-index: 10;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                            class="eye-icon" viewBox="0 0 16 16">
                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z" />
                            <path
                                d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z" />
                        </svg>
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                            class="eye-slash-icon d-none" viewBox="0 0 16 16">
                            <path
                                d="m10.79 12.912-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7.029 7.029 0 0 0 2.79-.588zM5.21 3.088A7.028 7.028 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474L5.21 3.089z" />
                            <path
                                d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829l-2.83-2.829zm4.95.708-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829zm3.171 6-12-12 .708-.708 12 12-.708.708z" />
                        </svg>
                    </span>
                </div>
            </div>

            <div class="login-btn-wrapper">
                <?= Html::submitButton('Save Password', [
                    'class' => 'btn-login',
                    'name' => 'new-password-button',
                    'id' => 'new-password-submit-btn',
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>
<?php
$js = <<<JS
    document.querySelectorAll('.toggle-password').forEach(function(toggle) {
        toggle.addEventListener('click', function (e) {
            const targetId = this.getAttribute('data-target');
            const passwordInput = document.getElementById(targetId);
            const eyeIcon = this.querySelector('.eye-icon');
            const eyeSlashIcon = this.querySelector('.eye-slash-icon');
            if (passwordInput.getAttribute('type') === 'password') {
                passwordInput.setAttribute('type', 'text');
                eyeIcon.classList.add('d-none');
                eyeSlashIcon.classList.remove('d-none');
            } else {
                passwordInput.setAttribute('type', 'password');
                eyeIcon.classList.remove('d-none');
                eyeSlashIcon.classList.add('d-none');
            }
        });
    });
JS;
$this->registerJs($js);
?>