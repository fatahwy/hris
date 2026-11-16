<?php

use app\helpers\GeneralHelper;
use app\helpers\RoleHelper;
use yii\bootstrap5\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\trx\Subscription $model */

$months = $model->getEffectiveMonths();
$paketName = $model->paket->nama_paket ?? '-';
$companyName = $model->company->name ?? '-';
$formattedPrice = (float) $model->price > 0 ? 'Rp ' . number_format((float) $model->price, 0, ',', '.') : 'Gratis';

$this->title = 'Detail Subscription: ' . $paketName . ' (' . $months . ' Bulan)';
$this->params['breadcrumbs'][] = ['label' => 'Subscription', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$bankName = GeneralHelper::BANK_NAME;
$bankAccountNumber = trim(GeneralHelper::BANK_ACCOUNT_NUMBER);
$bankAccountName = GeneralHelper::BANK_ACCOUNT_NAME;
$adminPhone = GeneralHelper::ADMIN_NO_PHONE;

$waClean = preg_replace('/[^0-9]/', '', $adminPhone);
if (strpos($waClean, '0') === 0) {
    $waClean = '62' . substr($waClean, 1);
}
$waMessage = "Halo Admin " . Yii::$app->name . ",\n\n"
    . "Saya ingin mengirimkan bukti transfer perpanjangan subscription:\n"
    . "- Perusahaan: " . $companyName . "\n"
    . "- Paket: " . $paketName . "\n"
    . "- Durasi: " . $months . " Bulan\n"
    . "- Total Transfer: " . $formattedPrice . "\n"
    . "- Bank Tujuan: " . $bankName . " (" . $bankAccountNumber . " a/n " . $bankAccountName . ")\n\n"
    . "Mohon untuk diverifikasi dan dilakukan approval. Terima kasih.";
$waUrl = 'https://wa.me/' . $waClean . '?text=' . rawurlencode($waMessage);
?>

<div class="subscription-view">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <?= Html::a('<i class="bi bi-arrow-left me-1"></i> Kembali', ['index'], ['class' => 'btn btn-outline-secondary px-3 shadow-sm']) ?>

        <div class="d-flex flex-wrap gap-2">
            <?php if ($model->isPending()): ?>
                <?php if (RoleHelper::isSuper()): ?>
                    <?= Html::a('<i class="bi bi-check-lg me-1"></i> Approve', ['approve', 'id' => $model->uuid], [
                        'class' => 'btn btn-success shadow-sm',
                        'data' => [
                            'confirm' => 'Apakah Anda yakin ingin menyetujui perpanjangan subscription ini?',
                            'method' => 'post',
                        ],
                    ]) ?>
                <?php endif; ?>
                <?= Html::a('<i class="bi bi-x-circle me-1"></i> Batalkan', ['cancel', 'id' => $model->uuid], [
                    'class' => 'btn btn-outline-danger shadow-sm',
                    'data' => [
                        'confirm' => 'Apakah Anda yakin ingin membatalkan pengajuan subscription ini?',
                        'method' => 'post',
                    ],
                ]) ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="card modern-form-card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-receipt text-primary"></i> Rincian Subscription
                </h5>
                <span class="text-muted small">Informasi pengajuan perpanjangan dan status persetujuan</span>
            </div>
            <div>
                <?= $model->getStatusLabel() ?>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <?= DetailView::widget([
                    'model' => $model,
                    'options' => ['class' => 'table table-modern-detail mb-0'],
                    'attributes' => [
                        [
                            'attribute' => 'id_company',
                            'label' => 'Perusahaan',
                            'format' => 'raw',
                            'value' => function ($m) {
                                $expired = ($m->company && !empty($m->company->expired_at))
                                    ? Yii::$app->formatter->asDatetime($m->company->expired_at)
                                    : '-';
                                return '<div class="fw-bold text-dark">' . Html::encode($m->company->name ?? '-') . '</div>'
                                    . '<span class="text-muted small">Masa aktif perusahaan saat ini: ' . Html::encode($expired) . '</span>';
                            },
                        ],
                        [
                            'attribute' => 'id_paket',
                            'label' => 'Paket',
                            'format' => 'raw',
                            'value' => function ($m) {
                                $maxUser = $m->paket ? (int) $m->paket->max_user : '-';
                                return '<span class="badge bg-light text-dark border px-2.5 py-1.5"><i class="bi bi-box-seam text-primary me-1"></i>' . Html::encode($m->paket->nama_paket ?? '-') . '</span>'
                                    . ' <span class="text-muted small ms-2">Maks ' . $maxUser . ' User</span>';
                            },
                        ],
                        [
                            'attribute' => 'jumlah_bulan',
                            'label' => 'Durasi Perpanjangan',
                            'format' => 'raw',
                            'value' => function ($m) use ($months) {
                                return '<span class="badge bg-light text-secondary border font-monospace px-2.5 py-1.5">' . $months . ' Bulan (' . (int) $m->durasi . ' Hari)</span>';
                            },
                        ],
                        [
                            'attribute' => 'price',
                            'label' => 'Total Pembayaran',
                            'format' => 'raw',
                            'value' => function ($m) use ($formattedPrice) {
                                return '<span class="fw-bold text-primary fs-5">' . Html::encode($formattedPrice) . '</span>';
                            },
                        ],
                        [
                            'attribute' => 'created_at',
                            'label' => 'Tanggal Pengajuan',
                            'format' => 'datetime',
                        ],
                        [
                            'attribute' => 'approved_by',
                            'label' => 'Diproses Oleh',
                            'value' => function ($m) {
                                return $m->approver->name ?? '-';
                            },
                            'visible' => !empty($model->approved_by),
                        ],
                        [
                            'attribute' => 'approved_at',
                            'label' => 'Waktu Diproses',
                            'format' => 'datetime',
                            'visible' => !empty($model->approved_at),
                        ],
                        [
                            'attribute' => 'note',
                            'format' => 'ntext',
                            'visible' => !empty($model->note),
                        ],
                    ],
                ]) ?>
            </div>
        </div>
    </div>

    <?php if ($model->isPending()): ?>
        <div class="card modern-form-card shadow-sm border-0">
            <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-bank text-primary"></i> Instruksi Transfer Pembayaran
                    </h5>
                    <span class="text-muted small">Silakan lakukan transfer sesuai nominal di bawah ini agar dapat segera di-approve oleh Admin</span>
                </div>
                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2.5 py-1.5">
                    <i class="bi bi-hourglass-split me-1"></i>Menunggu Transfer
                </span>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-12 col-lg-7">
                        <div class="p-4 rounded-3 border h-100" style="background-color: #f8fafc;">
                            <div class="small text-muted text-uppercase fw-semibold mb-3">Rekening Tujuan Resmi</div>

                            <div class="mb-3">
                                <div class="small text-muted">Bank Tujuan</div>
                                <div class="fw-bold text-dark fs-5"><?= Html::encode($bankName) ?></div>
                            </div>

                            <div class="mb-3">
                                <div class="small text-muted">Nomor Rekening</div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="fw-bold text-primary fs-4 font-monospace" id="bank-account-number"><?= Html::encode($bankAccountNumber) ?></span>
                                    <button type="button" class="btn btn-sm btn-outline-primary copy-btn" data-copy="<?= Html::encode($bankAccountNumber) ?>">
                                        <i class="bi bi-clipboard me-1"></i>Salin
                                    </button>
                                </div>
                            </div>

                            <div>
                                <div class="small text-muted">Atas Nama (A/N)</div>
                                <div class="fw-semibold text-dark"><?= Html::encode($bankAccountName) ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-5">
                        <div class="p-4 rounded-3 border h-100 d-flex flex-column justify-content-between" style="background-color: #ffffff;">
                            <div>
                                <div class="small text-muted text-uppercase fw-semibold mb-2">Total Yang Harus Ditransfer</div>
                                <div class="d-flex align-items-center gap-2 flex-wrap mb-3">
                                    <span class="fw-bold text-dark fs-3"><?= Html::encode($formattedPrice) ?></span>
                                    <button type="button" class="btn btn-sm btn-outline-secondary copy-btn" data-copy="<?= (int) $model->price ?>">
                                        <i class="bi bi-clipboard me-1"></i>Salin Nominal
                                    </button>
                                </div>
                                <p class="small text-muted mb-4">
                                    Setelah melakukan transfer, kirimkan bukti pembayaran ke admin melalui WhatsApp untuk mempercepat proses approval.
                                </p>
                            </div>

                            <div class="d-grid gap-2">
                                <?= Html::a('<i class="bi bi-whatsapp me-2"></i>Konfirmasi Bukti Transfer via WhatsApp', $waUrl, [
                                    'class' => 'btn btn-success py-2 shadow-sm fw-semibold',
                                    'target' => '_blank',
                                    'rel' => 'noopener noreferrer',
                                ]) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>

<?php
$js = <<<JS
document.querySelectorAll('.copy-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var text = this.getAttribute('data-copy');
        var originalHtml = this.innerHTML;
        var el = this;
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(function() {
                el.innerHTML = '<i class="bi bi-check2 me-1"></i>Tersalin';
                setTimeout(function() { el.innerHTML = originalHtml; }, 1500);
            });
        }
    });
});
JS;
$this->registerJs($js);
?>
