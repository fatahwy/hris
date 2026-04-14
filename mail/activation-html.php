<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\master\Client $client */

$activationLink = Yii::$app->urlManager->createAbsoluteUrl(['site/activate', 'token' => $client->token]);
?>
<div class="activation-email">
    <p>Halo <?= Html::encode($client->name) ?>,</p>

    <p>Terima kasih telah mendaftar di <?= Html::encode(Yii::$app->name) ?>. Silakan klik link di bawah ini untuk
        mengaktifkan akun perusahaan Anda:</p>

    <p><?= Html::a(Html::encode($activationLink), $activationLink) ?></p>

    <p>Link ini akan kadaluarsa dalam 7 hari.</p>
</div>