<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/** @var yii\web\View $this */
/** @var app\models\trx\Schedule $model */
/** @var string $type */

$type = $type ?? 'in';
$isClockIn = ($type === 'in');
$typeName = $isClockIn ? 'Clock In' : 'Clock Out';
$this->title = $typeName . ' - ' . date('d M Y', strtotime($model->date));
$this->params['breadcrumbs'][] = ['label' => 'Presensi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$saveUrl = Url::to(['save']);
$indexUrl = Url::to(['index']);
?>

<div class="attendance-clock content-dashboard pb-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">
            <div class="card shadow border-0 rounded-4 overflow-hidden bg-white">
                <div class="card-header <?= $isClockIn ? 'bg-primary' : 'bg-warning' ?> <?= $isClockIn ? 'text-white' : 'text-dark' ?> text-center py-3 px-4">
                    <div class="d-inline-flex align-items-center gap-2 mb-1">
                        <i class="bi <?= $isClockIn ? 'bi-box-arrow-in-right' : 'bi-box-arrow-right' ?> fs-4"></i>
                        <h4 class="mb-0 fw-bold"><?= Html::encode($typeName) ?></h4>
                    </div>
                    <div class="small opacity-90 d-flex justify-content-center align-items-center gap-2">
                        <span class="badge <?= $isClockIn ? 'bg-white text-primary' : 'bg-dark text-white' ?> px-2 py-1 font-monospace">
                            Shift: <?= Html::encode($model->shift_name) ?>
                        </span>
                        <span>&bull;</span>
                        <span><?= date('d M Y', strtotime($model->date)) ?></span>
                    </div>
                </div>
                <div class="card-body p-3 p-sm-4 text-center">
                    
                    <!-- Camera Viewport with Scanner Overlay -->
                    <div id="camera-container" class="position-relative mb-3 bg-dark rounded-4 shadow-inner" style="overflow: hidden; height: 320px; max-height: 50vh; display: flex; align-items: center; justify-content: center;">
                        <video id="videoElement" autoplay playsinline style="width: 100%; height: 100%; object-fit: cover;"></video>
                        <canvas id="canvasElement" class="d-none" style="width: 100%; height: 100%; object-fit: cover; position: absolute; top:0; left:0; z-index: 10;"></canvas>
                        
                        <!-- Scanner Frame Guide Overlay -->
                        <div class="position-absolute border border-2 border-white rounded-4 pointer-events-none opacity-50"
                            style="width: 70%; height: 75%; z-index: 4; border-style: dashed !important;">
                        </div>

                        <div id="camera-loader" class="position-absolute d-flex flex-column align-items-center justify-content-center" style="z-index: 5;">
                            <div class="spinner-border text-light" role="status"></div>
                            <span class="mt-2 text-white small opacity-75">Mengakses Kamera...</span>
                        </div>
                    </div>

                    <!-- Location Status Card -->
                    <div class="location-status mb-3 text-start bg-light p-3 rounded-3 border">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <small class="text-muted fw-semibold d-flex align-items-center gap-1">
                                <i class="bi bi-geo-alt-fill text-danger"></i> Deteksi Lokasi GPS:
                            </small>
                            <span class="badge bg-white text-secondary border px-2 py-0.5 small" id="loc-status-pill">GPS Aktif</span>
                        </div>
                        <div id="location-text" class="small fw-bold placeholder-glow">
                            <span class="placeholder col-8"></span>
                        </div>
                        <input type="hidden" id="lat" name="lat" value="">
                        <input type="hidden" id="lng" name="lng" value="">
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2">
                        <button type="button" id="btn-capture" class="btn btn-primary btn-lg rounded-3 py-2.5 fw-semibold shadow-sm">
                            <i class="bi bi-camera me-1"></i> Ambil Foto Wajah
                        </button>
                        <button type="button" id="btn-retake" class="btn btn-outline-secondary btn-lg rounded-3 py-2.5 fw-semibold d-none">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Ulangi Foto
                        </button>
                        <button type="button" id="btn-submit" class="btn <?= $isClockIn ? 'btn-success' : 'btn-warning text-dark' ?> btn-lg rounded-3 py-2.5 fw-bold d-none shadow-sm" disabled>
                            <i class="bi bi-check-circle me-1"></i> Konfirmasi <?= $typeName ?> Sekarang
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php
$js = <<<JS
$(document).ready(function() {
    const video = document.getElementById('videoElement');
    const canvas = document.getElementById('canvasElement');
    const btnCapture = document.getElementById('btn-capture');
    const btnRetake = document.getElementById('btn-retake');
    const btnSubmit = document.getElementById('btn-submit');
    const locationText = document.getElementById('location-text');
    const loader = document.getElementById('camera-loader');
    const latInput = document.getElementById('lat');
    const lngInput = document.getElementById('lng');
    
    let photoData = null;
    let stream = null;

    // Initialize Camera
    async function initCamera() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: "user" },
                audio: false
            });
            video.srcObject = stream;
            video.onloadedmetadata = () => {
                video.play();
                loader.classList.add('d-none');
            };
        } catch (err) {
            console.error("Camera Error: ", err);
            loader.innerHTML = '<div class="alert alert-danger p-2 m-2 small">Gagal mengakses kamera. Izinkan izin kamera pada browser Anda.</div>';
        }
    }

    // Capture location
    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    latInput.value = lat;
                    lngInput.value = lng;
                    locationText.innerHTML = '<span class="text-success"><i class="bi bi-check-circle-fill me-1"></i> ' + parseFloat(lat).toFixed(5) + ', ' + parseFloat(lng).toFixed(5) + '</span>';
                    
                    // Enable submit if photo is already taken
                    if (photoData) {
                        btnSubmit.removeAttribute('disabled');
                    }
                },
                (error) => {
                    console.error("Geolocation Error: ", error);
                    locationText.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle-fill me-1"></i> Gagal mendapatkan lokasi. Izinkan akses GPS.</span>';
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        } else {
            locationText.innerHTML = '<span class="text-danger">Geolocation tidak didukung browser ini.</span>';
        }
    }

    initCamera();
    getLocation();

    // Capture Picture
    btnCapture.addEventListener('click', function() {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        photoData = canvas.toDataURL('image/jpeg', 0.8);
        
        video.classList.add('d-none');
        canvas.classList.remove('d-none');
        
        btnCapture.classList.add('d-none');
        btnRetake.classList.remove('d-none');
        btnSubmit.classList.remove('d-none');
        
        // If location is ready
        if (latInput.value && lngInput.value) {
            btnSubmit.removeAttribute('disabled');
        }
    });

    // Retake Picture
    btnRetake.addEventListener('click', function() {
        photoData = null;
        canvas.classList.add('d-none');
        video.classList.remove('d-none');
        
        btnCapture.classList.remove('d-none');
        btnRetake.classList.add('d-none');
        btnSubmit.classList.add('d-none');
        btnSubmit.setAttribute('disabled', 'disabled');
    });

    // Submit Attendance
    btnSubmit.addEventListener('click', function() {
        if (!photoData || !latInput.value || !lngInput.value) {
            alert("Tunggu sampai foto dan lokasi tersedia.");
            return;
        }
        
        btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menyimpan...';
        btnSubmit.setAttribute('disabled', 'disabled');
        btnRetake.setAttribute('disabled', 'disabled');
        
        $.ajax({
            url: '{$saveUrl}',
            type: 'POST',
            data: {
                photo: photoData,
                lat: latInput.value,
                lng: lngInput.value,
                [yii.getCsrfParam()]: yii.getCsrfToken()
            },
            success: function(response) {
                if (response.success) {
                    window.location.href = '{$indexUrl}';
                } else {
                    alert(response.message || 'Terjadi kesalahan saat menyimpan data.');
                    btnSubmit.innerHTML = '<i class="bi bi-check-circle me-1"></i> Konfirmasi {$typeName} Sekarang';
                    btnSubmit.removeAttribute('disabled');
                    btnRetake.removeAttribute('disabled');
                }
            },
            error: function() {
                alert('Terjadi kesalahan jaringan.');
                btnSubmit.innerHTML = '<i class="bi bi-check-circle me-1"></i> Konfirmasi {$typeName} Sekarang';
                btnSubmit.removeAttribute('disabled');
                btnRetake.removeAttribute('disabled');
            }
        });
    });
    
    // Cleanup on exit
    $(window).on('beforeunload', function() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
    });
});
JS;
$this->registerJs($js, View::POS_END);
?>
