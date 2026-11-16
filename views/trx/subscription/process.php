<?php

use app\helpers\GeneralHelper;
use app\helpers\RoleHelper;
use kartik\form\ActiveForm;
use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var app\models\trx\Subscription $model */
/** @var app\models\master\Company|null $company */
/** @var app\models\master\Paket[] $pakets */
/** @var app\models\master\Paket|null $currentPaket */
/** @var array $companyList */

$this->title = 'Perpanjang Subscription';
$this->params['breadcrumbs'][] = ['label' => 'Subscription', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$paketOptions = [];
$paketData = [];
foreach ($pakets as $p) {
    $paketOptions[$p->id_paket] = $p->getDropdownLabel();
    $paketData[$p->id_paket] = [
        'name' => $p->nama_paket,
        'price' => (float) $p->price,
        'max_user' => (int) $p->max_user,
        'durasi_hari' => (int) $p->durasi_hari,
    ];
}

$currentMaxUser = $currentPaket ? (int) $currentPaket->max_user : 0;
$currentExpired = ($company && !empty($company->expired_at)) ? $company->expired_at : null;
$isExpired = $company ? $company->isExpired() : true;
$baseDateLabel = (!$isExpired && $currentExpired)
    ? Yii::$app->formatter->asDatetime($currentExpired) . ' (sisa masa aktif)'
    : 'Hari ini (' . date('d F Y') . ')';
$todayLabel = 'Hari ini (' . date('d F Y') . ')';
?>

<div class="subscription-process">
    <div class="card modern-form-card shadow-sm border-0">
        <div class="card-header bg-white py-3 px-4 border-bottom">
            <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-arrow-repeat text-primary"></i> <?= Html::encode($this->title) ?>
            </h5>
            <span class="text-muted small">Pilih paket dan tentukan jumlah bulan perpanjangan sesuai kebutuhan Anda</span>
        </div>
        <div class="card-body p-4">

            <?php if ($company): ?>
                <div class="p-3 rounded-3 border mb-4" style="background-color: #f8fafc;">
                    <div class="row g-2 align-items-center">
                        <div class="col-12 col-md-4">
                            <div class="small text-muted">Perusahaan</div>
                            <div class="fw-bold text-dark"><?= Html::encode($company->name) ?></div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="small text-muted">Paket Saat Ini</div>
                            <div class="fw-semibold text-dark">
                                <?= $currentPaket ? Html::encode($currentPaket->nama_paket) : '-' ?>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="small text-muted">Masa Aktif Saat Ini</div>
                            <div class="fw-semibold text-dark">
                                <?= $currentExpired ? Html::encode(Yii::$app->formatter->asDatetime($currentExpired)) : '-' ?>
                                <?php if ($isExpired): ?>
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 ms-1">Kadaluarsa</span>
                                <?php else: ?>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 ms-1">Aktif</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php $form = ActiveForm::begin(); ?>

            <div class="row g-3">
                <?php if (RoleHelper::isSuper() && !empty($companyList)): ?>
                    <div class="col-12 col-md-6">
                        <?= $form->field($model, 'id_company')->dropDownList($companyList, [
                            'class' => 'form-select',
                            'prompt' => '-- Pilih Perusahaan --',
                        ]) ?>
                    </div>
                <?php endif; ?>

                <div class="col-12 col-md-6">
                    <?= $form->field($model, 'id_paket')->dropDownList($paketOptions, [
                        'class' => 'form-select',
                        'id' => 'subscription-id-paket',
                    ]) ?>
                </div>

                <div class="col-12 col-md-6">
                    <?= $form->field($model, 'jumlah_bulan')->textInput([
                        'type' => 'number',
                        'min' => 1,
                        'max' => 120,
                        'id' => 'subscription-jumlah-bulan',
                        'placeholder' => 'Contoh: 1, 2, 3, 6, 12',
                    ])->hint('Masukkan jumlah bulan (1, 2, 3, atau bebas sesuai kebutuhan).') ?>

                    <div class="d-flex flex-wrap gap-2 mt-1">
                        <?php foreach ([1, 2, 3, 6, 12] as $preset): ?>
                            <button type="button" class="btn btn-sm btn-outline-primary preset-bulan" data-months="<?= $preset ?>">
                                <?= $preset ?> Bulan
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="col-12">
                    <?= $form->field($model, 'note')->textarea([
                        'rows' => 2,
                        'placeholder' => 'Catatan tambahan (opsional)...',
                    ]) ?>
                </div>
            </div>

            <!-- Live Summary Box -->
            <div class="p-3 p-sm-4 rounded-3 border mt-3" style="background-color: #f8fafc;">
                <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-receipt text-primary"></i> Ringkasan Perpanjangan
                </h6>
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="small text-muted">Paket Dipilih</div>
                        <div class="fw-semibold text-dark" id="summary-paket">-</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="small text-muted">Durasi</div>
                        <div class="fw-semibold text-dark" id="summary-durasi">-</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="small text-muted">Batas User</div>
                        <div class="fw-semibold text-dark" id="summary-max-user">-</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="small text-muted">Total Pembayaran</div>
                        <div class="fw-bold text-primary fs-5" id="summary-total">Rp 0</div>
                    </div>
                </div>
                <div class="small text-muted mt-3 pt-2 border-top" id="summary-expired-note">
                    <i class="bi bi-info-circle me-1 text-primary"></i>
                    Setelah disetujui oleh Admin, masa aktif akan ditambah sesuai jumlah bulan dari <strong><?= Html::encode($baseDateLabel) ?></strong>.
                </div>
            </div>

            <div class="d-flex flex-column-reverse flex-sm-row justify-content-end gap-2 mt-4 pt-3 border-top">
                <?= Html::a('Batal', ['index'], ['class' => 'btn btn-outline-secondary px-4 text-center']) ?>
                <?= Html::submitButton('<i class="bi bi-send me-1"></i> Ajukan Perpanjangan', ['class' => 'btn btn-primary px-4 shadow-sm text-center']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php
$paketJson = json_encode($paketData);
$baseDateJson = json_encode($baseDateLabel);
$todayJson = json_encode($todayLabel);
$js = <<<JS
(function() {
    var paketMap = {$paketJson};
    var currentMaxUser = {$currentMaxUser};
    var baseDateLabel = {$baseDateJson};
    var todayLabel = {$todayJson};
    var paketEl = document.getElementById('subscription-id-paket');
    var bulanEl = document.getElementById('subscription-jumlah-bulan');
    var noteEl = document.getElementById('summary-expired-note');

    function formatRupiah(num) {
        return 'Rp ' + Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function updateSummary() {
        var pid = paketEl ? paketEl.value : null;
        var months = parseInt(bulanEl ? bulanEl.value : 1, 10);
        if (isNaN(months) || months < 1) months = 1;

        var info = paketMap[pid];
        if (!info) return;

        document.getElementById('summary-paket').textContent = info.name;
        document.getElementById('summary-durasi').textContent = months + ' Bulan (' + (months * info.durasi_hari) + ' Hari)';
        document.getElementById('summary-max-user').textContent = info.max_user + ' User';
        document.getElementById('summary-total').textContent = formatRupiah(info.price * months);

        if (noteEl) {
            if (currentMaxUser > 0 && info.max_user > currentMaxUser) {
                noteEl.innerHTML = '<i class="bi bi-arrow-up-circle me-1 text-warning"></i> <strong>Naik Paket (Upgrade):</strong> Setelah disetujui oleh Admin, masa aktif akan <strong>direset</strong> mulai dari <strong>' + todayLabel + '</strong> ditambah ' + months + ' bulan.';
            } else {
                noteEl.innerHTML = '<i class="bi bi-info-circle me-1 text-primary"></i> <strong>Paket Sama (Akumulasi):</strong> Setelah disetujui oleh Admin, masa aktif akan ditambah ' + months + ' bulan dari <strong>' + baseDateLabel + '</strong>.';
            }
        }
    }

    if (paketEl) paketEl.addEventListener('change', updateSummary);
    if (bulanEl) bulanEl.addEventListener('input', updateSummary);

    document.querySelectorAll('.preset-bulan').forEach(function(btn) {
        btn.addEventListener('click', function() {
            if (bulanEl) {
                bulanEl.value = this.getAttribute('data-months');
                updateSummary();
            }
        });
    });

    updateSummary();
})();
JS;
$this->registerJs($js);
?>
