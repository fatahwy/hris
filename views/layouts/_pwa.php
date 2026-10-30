<?php

use yii\helpers\Url;

?>
<!-- PWA Install Banner (Bottom Floating) -->
<div id="pwaInstallBanner" class="pwa-install-banner">
    <div class="pwa-banner-icon">
        <img src="<?= Url::to('@web/images/icons/icon-96x96.png') ?>" alt="PRANATA HR">
    </div>
    <div class="pwa-banner-content">
        <div class="pwa-banner-title">Install PRANATA HR</div>
        <div class="pwa-banner-subtitle">Pasang di HP untuk absensi cepat tanpa buka browser.</div>
    </div>
    <div class="pwa-banner-actions">
        <button type="button" class="pwa-btn-install pwa-install-trigger">
            <i class="bi bi-download"></i> Install
        </button>
        <button type="button" class="pwa-btn-close pwa-dismiss-btn" aria-label="Tutup">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
</div>

<!-- iOS Safari Installation Instructions Modal -->
<div id="pwaIosModal" class="pwa-ios-overlay">
    <div class="pwa-ios-modal">
        <div class="pwa-ios-header">
            <h5 class="pwa-ios-title">
                <i class="bi bi-phone text-primary"></i> Pasang di iPhone / iPad
            </h5>
            <button type="button" class="btn-close pwa-close-ios" aria-label="Tutup"></button>
        </div>
        <p class="text-muted small mb-3">Ikuti 3 langkah mudah berikut untuk menambahkan PRANATA HR ke Layar Utama:</p>
        <ul class="pwa-ios-steps">
            <li>
                <span class="pwa-step-num">1</span>
                <div>Ketuk tombol <b>Bagikan (Share)</b> <span class="pwa-ios-badge"><i class="bi bi-box-arrow-up"></i></span> di menu bawah Safari.</div>
            </li>
            <li>
                <span class="pwa-step-num">2</span>
                <div>Gulir ke bawah dan ketuk <span class="pwa-ios-badge"><i class="bi bi-plus-square"></i> Tambahkan ke Layar Utama</span>.</div>
            </li>
            <li>
                <span class="pwa-step-num">3</span>
                <div>Ketuk <b>Tambah</b> di pojok kanan atas layar Anda.</div>
            </li>
        </ul>
        <button type="button" class="btn btn-primary w-100 rounded-3 py-2 fw-semibold pwa-close-ios">
            Mengerti
        </button>
    </div>
</div>
