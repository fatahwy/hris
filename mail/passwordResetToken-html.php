<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\master\User $user */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/new-password', 'token' => $user->token]);
?>
<div class="password-reset-email">
    <p>Halo <?= Html::encode($user->name) ?>,</p>

    <p>Kami menerima permintaan untuk mengatur ulang kata sandi akun Anda. Silakan klik link di bawah ini untuk melanjutkan:</p>

    <p><?= Html::a(Html::encode($resetLink), $resetLink) ?></p>

    <p>Jika Anda tidak merasa melakukan permintaan ini, silakan abaikan email ini.</p>
</div>
