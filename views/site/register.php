<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\RegisterForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Register';

// Register the custom CSS
$this->registerCssFile('@web/css/register.css', ['depends' => [\app\assets\AppAsset::class]]);
?>

<div class="register-container">
    <!-- Left Panel: Form -->
    <div class="register-left">
        <div class="register-form-wrapper">
            <h1 class="register-title">Nice to meet you!</h1>
            <p class="register-subtitle">Let's Sign In Your Account</p>

            <?php
            $form = ActiveForm::begin([
                'id' => 'register-form',
                'options' => ['class' => 'register-form'],
                'fieldConfig' => [
                    'template' => "{label}\n{input}\n{error}",
                    'labelOptions' => ['class' => ''],
                    'inputOptions' => ['class' => 'form-control'],
                    'errorOptions' => ['class' => 'invalid-feedback d-block'],
                ],
            ]); ?>

            <div class="register-form-grid">
                <div class="register-form-group">
                    <?= $form->field($model, 'nama_lengkap')->textInput([
                        'placeholder' => 'Masukkan nama lengkap',
                        'autofocus' => true,
                        'id' => 'register-nama-lengkap',
                    ]) ?>
                </div>

                <div class="register-form-group">
                    <?= $form->field($model, 'nama_instansi')->textInput([
                        'placeholder' => 'Masukkan nama instansi',
                        'id' => 'register-nama-instansi',
                    ]) ?>
                </div>

                <div class="register-form-group">
                    <?= $form->field($model, 'no_wa')->textInput([
                        'placeholder' => 'Contoh: 081234567890',
                        'id' => 'register-no-wa',
                    ]) ?>
                </div>

                <div class="register-form-group">
                    <?= $form->field($model, 'email')->textInput([
                        'type' => 'email',
                        'placeholder' => 'contoh@email.com',
                        'id' => 'register-email',
                    ]) ?>
                </div>

                <div class="register-form-group">
                    <?= $form->field($model, 'password', [
                        'template' => "{label}\n<div class=\"position-relative\">\n{input}\n<span class=\"toggle-password position-absolute\" data-target=\"register-password\" style=\"right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #b0aec8; z-index: 10;\">" .
                            '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="eye-icon" viewBox="0 0 16 16"><path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/><path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/></svg>' .
                            '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="eye-slash-icon d-none" viewBox="0 0 16 16"><path d="m10.79 12.912-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7.029 7.029 0 0 0 2.79-.588zM5.21 3.088A7.028 7.028 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474L5.21 3.089z"/><path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829l-2.83-2.829zm4.95.708-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829zm3.171 6-12-12 .708-.708 12 12-.708.708z"/></svg>' .
                            "</span>\n</div>\n{error}",
                    ])->passwordInput([
                                'placeholder' => 'Minimal 6 karakter',
                                'id' => 'register-password',
                                'style' => 'padding-right: 45px;'
                            ]) ?>
                </div>

                <div class="register-form-group">
                    <?= $form->field($model, 'confirm_password', [
                        'template' => "{label}\n<div class=\"position-relative\">\n{input}\n<span class=\"toggle-password position-absolute\" data-target=\"register-confirm-password\" style=\"right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #b0aec8; z-index: 10;\">" .
                            '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="eye-icon" viewBox="0 0 16 16"><path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/><path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/></svg>' .
                            '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="eye-slash-icon d-none" viewBox="0 0 16 16"><path d="m10.79 12.912-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7.029 7.029 0 0 0 2.79-.588zM5.21 3.088A7.028 7.028 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474L5.21 3.089z"/><path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829l-2.83-2.829zm4.95.708-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829zm3.171 6-12-12 .708-.708 12 12-.708.708z"/></svg>' .
                            "</span>\n</div>\n{error}",
                    ])->passwordInput([
                                'placeholder' => 'Ulangi password',
                                'id' => 'register-confirm-password',
                                'style' => 'padding-right: 45px;'
                            ]) ?>
                </div>
            </div>

            <div class="register-bottom">
                <div class="register-login-link">
                    Already have an account?
                    <?= Html::a('Login Now', ['/site/login'], ['id' => 'register-login-link']) ?>
                </div>
                <?= Html::submitButton('Sign UP', [
                    'class' => 'btn-register',
                    'name' => 'register-button',
                    'id' => 'register-submit-btn',
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <!-- Right Panel: Illustration -->
    <div class="register-right">
        <img src="<?= Yii::getAlias('@web/images/register-illustration.png') ?>" alt="Data Analytics Illustration"
            class="register-illustration" id="register-illustration">
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