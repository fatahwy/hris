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
                <i class="bi bi-apple text-dark"></i> Pasang di iPhone / iPad
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
                <div>Gulir ke bawah dan ketuk opsi <span class="pwa-ios-badge"><i class="bi bi-plus-square"></i> Tambahkan ke Layar Utama</span>.</div>
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

<!-- Android / Chrome Manual Instructions Modal (For HTTP / fallback) -->
<div id="pwaAndroidModal" class="pwa-ios-overlay">
    <div class="pwa-ios-modal">
        <div class="pwa-ios-header">
            <h5 class="pwa-ios-title">
                <i class="bi bi-phone text-primary"></i> Pasang di Layar Utama HP
            </h5>
            <button type="button" class="btn-close pwa-close-android" aria-label="Tutup"></button>
        </div>
        <p class="text-muted small mb-3">Ikuti langkah praktis berikut untuk memasang aplikasi di ponsel Anda:</p>
        <ul class="pwa-ios-steps">
            <li>
                <span class="pwa-step-num">1</span>
                <div>Ketuk ikon <b>titik tiga (⋮)</b> <span class="pwa-ios-badge"><i class="bi bi-three-dots-vertical"></i></span> di pojok kanan atas browser Chrome.</div>
            </li>
            <li>
                <span class="pwa-step-num">2</span>
                <div>Pilih menu <b>"Install aplikasi"</b> atau <b>"Tambahkan ke Layar Utama"</b> (<i>Add to Home screen</i>).</div>
            </li>
            <li>
                <span class="pwa-step-num">3</span>
                <div>Ketuk tombol <b>Tambah / Install</b> untuk memasang ikon di layar HP.</div>
            </li>
        </ul>
        <div class="alert alert-light border small text-muted mb-3 py-2 px-3 rounded-3" style="font-size: 0.78rem; line-height: 1.45;">
            <i class="bi bi-info-circle text-primary me-1"></i>
            <b>Catatan Koneksi:</b> Google Chrome mewajibkan koneksi <b>HTTPS</b> untuk memunculkan tombol instalasi otomatis. Pada koneksi HTTP, Chrome menggunakan opsi <i>"Tambahkan ke Layar Utama"</i>.
        </div>
        <button type="button" class="btn btn-primary w-100 rounded-3 py-2 fw-semibold pwa-close-android">
            Mengerti
        </button>
    </div>
</div>
