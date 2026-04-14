<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

$typeName = $type === 'in' ? 'Clock In' : 'Clock Out';
$this->title = $typeName . ' - ' . date('d M Y', strtotime($model->date));
$this->params['breadcrumbs'][] = ['label' => 'Attendance', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$saveUrl = Url::to(['save-clock', 'id' => $model->id_schedule, 'type' => $type]);
$indexUrl = Url::to(['index']);
?>

<div class="attendance-clock content-dashboard">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h4 class="mb-0"><i class="fas fa-camera"></i> <?= Html::encode($typeName) ?></h4>
                    <small>Shift: <?= Html::encode($model->shift_name) ?></small>
                </div>
                <div class="card-body p-4 text-center">
                    
                    <div id="camera-container" class="position-relative mb-3 bg-light rounded" style="overflow: hidden; height: 320px; display: flex; align-items: center; justify-content: center;">
                        <video id="videoElement" autoplay playsinline style="width: 100%; height: 100%; object-fit: cover;"></video>
                        <canvas id="canvasElement" class="d-none" style="width: 100%; height: 100%; object-fit: cover; position: absolute; top:0; left:0; z-index: 10;"></canvas>
                        
                        <div id="camera-loader" class="position-absolute d-flex flex-column align-items-center justify-content-center" style="z-index: 5;">
                            <div class="spinner-border text-primary" role="status"></div>
                            <span class="mt-2 text-muted">Akses Kamera...</span>
                        </div>
                    </div>

                    <div class="location-status mb-3 text-start bg-light p-2 rounded">
                        <small class="d-block text-muted mb-1"><i class="fas fa-map-marker-alt text-danger"></i> Lokasi Saat Ini:</small>
                        <div id="location-text" class="text-sm fw-bold placeholder-glow">
                            <span class="placeholder col-8"></span>
                        </div>
                        <input type="hidden" id="lat" name="lat" value="">
                        <input type="hidden" id="lng" name="lng" value="">
                    </div>

                    <div class="d-grid gap-2">
                        <button type="button" id="btn-capture" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-camera"></i> Ambil Foto
                        </button>
                        <button type="button" id="btn-retake" class="btn btn-outline-secondary btn-lg d-none">
                            <i class="fas fa-undo"></i> Ulangi Foto
                        </button>
                        <button type="button" id="btn-submit" class="btn btn-success btn-lg d-none" disabled>
                            <i class="fas fa-check-circle"></i> Konfirmasi <?= $typeName ?>
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
            loader.innerHTML = '<div class="alert alert-danger p-2 m-0 text-sm">Gagal mengakses kamera. Izinkan akses kamera pada browser Anda.</div>';
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
                    locationText.innerHTML = '<span class="text-success"><i class="fas fa-check"></i> ' + parseFloat(lat).toFixed(5) + ', ' + parseFloat(lng).toFixed(5) + '</span>';
                    
                    // Enable submit if photo is already taken
                    if (photoData) {
                        btnSubmit.removeAttribute('disabled');
                    }
                },
                (error) => {
                    console.error("Geolocation Error: ", error);
                    locationText.innerHTML = '<span class="text-danger"><i class="fas fa-times"></i> Gagal mendapatkan lokasi. Izinkan akses lokasi.</span>';
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
        
        btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';
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
                    btnSubmit.innerHTML = '<i class="fas fa-check-circle"></i> Konfirmasi {$typeName}';
                    btnSubmit.removeAttribute('disabled');
                    btnRetake.removeAttribute('disabled');
                }
            },
            error: function() {
                alert('Terjadi kesalahan jaringan.');
                btnSubmit.innerHTML = '<i class="fas fa-check-circle"></i> Konfirmasi {$typeName}';
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
