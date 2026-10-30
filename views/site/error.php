<?php

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception $exception */

use yii\helpers\Html;

$this->title = $name;
?>
<div class="site-error py-4">
    <div class="card shadow-sm border-0 rounded-4 mx-auto" style="max-width: 600px;">
        <div class="card-body p-4 text-center">
            <div class="text-danger mb-3" style="font-size: 3rem;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h1 class="h4 fw-bold text-dark mb-3"><?= Html::encode($this->title) ?></h1>
            <div class="alert alert-danger text-start">
                <?= nl2br(Html::encode($message)) ?>
            </div>
            <p class="text-muted small">
                Terjadi kesalahan saat server memproses permintaan Anda.<br>
                Jika Anda merasa ini adalah kesalahan sistem, silakan hubungi tim administrator.
            </p>
            <a href="<?= Yii::$app->homeUrl ?>" class="btn btn-primary px-4 mt-2">
                <i class="fas fa-home me-1"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</div>