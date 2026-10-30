<?php

use app\helpers\GeneralHelper;
use yii\bootstrap5\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\master\Account $model */
/** @var array $companyAllowances */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Data User', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$formatter = Yii::$app->formatter;
?>
<div class="user-view">

    <div class="d-flex justify-content-end align-items-center mb-4">
        <!-- <div>
            <h4 class="fw-bold mb-1 text-dark"><?= Html::encode($model->name) ?></h4>
            <span class="text-muted small">
                <i class="fas fa-id-badge me-1"></i> <?= Html::encode($model->employee_code ?: '-') ?> &bull; 
                <i class="fas fa-envelope ms-2 me-1"></i> <?= Html::encode($model->email) ?>
            </span>
        </div> -->
        <!-- <div> -->
        <?= Html::a('<i class="fas fa-arrow-left me-1"></i> Kembali', ['index'], ['class' => 'btn btn-outline-secondary me-2']) ?>
        <?= Html::a('<i class="fas fa-edit me-1"></i> Ubah Data', ['process', 'id' => $model->uuid], ['class' => 'btn btn-primary me-2']) ?>
        <?= Html::a('<i class="fas fa-trash me-1"></i> Hapus', ['delete', 'id' => $model->uuid], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Apakah Anda yakin ingin menghapus data karyawan ini?',
                'method' => 'post',
            ],
        ]) ?>
        <!-- </div> -->
    </div>

    <!-- User Header Card -->
    <!-- <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-4">
                <div class="flex-shrink-0">
                    <?php if (!empty($model->photo)): ?>
                        <img src="<?= Yii::getAlias('@web/' . $model->photo) ?>" alt="Avatar" class="rounded-circle border"
                            style="width: 80px; height: 80px; object-fit: cover;">
                    <?php else: ?>
                        <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center text-secondary"
                            style="width: 80px; height: 80px; font-size: 32px;">
                            <i class="fas fa-user"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <h5 class="fw-bold mb-0"><?= Html::encode($model->name) ?></h5>
                        <?= GeneralHelper::textLabel($model->status ? 'Active' : 'Inactive', $model->status) ?>
                        <?php if (!empty($model->employment_status)): ?>
                            <span class="badge bg-secondary"><?= Html::encode($model->employment_status) ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="text-muted small">
                        <span><i class="fas fa-building me-1"></i>
                            <?= Html::encode($model->company->name ?? '-') ?></span>
                        <span class="mx-2">&bull;</span>
                        <span><i class="fas fa-sitemap me-1"></i>
                            <?= Html::encode($model->department->name ?? '-') ?></span>
                        <span class="mx-2">&bull;</span>
                        <span><i class="fas fa-user-tag me-1"></i>
                            <?= Html::encode($model->position->name ?? '-') ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div> -->

    <!-- Tabs Container -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-header bg-white border-bottom p-0">
            <ul class="nav nav-tabs border-0 px-3 pt-2" id="viewUserTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-semibold" id="tab-pribadi-btn" data-bs-toggle="tab"
                        data-bs-target="#tab-pribadi-pane" type="button" role="tab">
                        <i class="fas fa-user me-2 text-primary"></i> Data Pribadi
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold" id="tab-pekerjaan-btn" data-bs-toggle="tab"
                        data-bs-target="#tab-pekerjaan-pane" type="button" role="tab">
                        <i class="fas fa-briefcase me-2 text-primary"></i> Pekerjaan
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold" id="tab-payroll-btn" data-bs-toggle="tab"
                        data-bs-target="#tab-payroll-pane" type="button" role="tab">
                        <i class="fas fa-money-bill-wave me-2 text-primary"></i> Payroll &amp; Pajak
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold" id="tab-bpjs-btn" data-bs-toggle="tab"
                        data-bs-target="#tab-bpjs-pane" type="button" role="tab">
                        <i class="fas fa-shield-alt me-2 text-primary"></i> BPJS
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body p-4">
            <div class="tab-content" id="viewUserTabsContent">

                <!-- TAB 1: DATA PRIBADI -->
                <div class="tab-pane fade show active" id="tab-pribadi-pane" role="tabpanel">

                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i
                            class="fas fa-user-circle text-primary me-2"></i> Akun</h6>
                    <div class="row mb-4">
                        <div class="col-md-6 mb-2"><strong>Nama:</strong> <?= Html::encode($model->name) ?></div>
                        <div class="col-md-6 mb-2"><strong>Email:</strong> <?= Html::encode($model->email) ?></div>
                        <div class="col-md-6 mb-2"><strong>No. Telepon / WA:</strong>
                            <?= Html::encode($model->phone ?: '-') ?>
                        </div>
                        <div class="col-md-6 mb-2"><strong>Role:</strong>
                            <?= Html::encode($model->role->item_name ?? '-') ?>
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i
                            class="fas fa-id-card text-primary me-2"></i> Identitas</h6>
                    <div class="row mb-4">
                        <div class="col-md-6 mb-2"><strong>NIK KTP:</strong> <?= Html::encode($model->nik ?: '-') ?>
                        </div>
                        <div class="col-md-6 mb-2"><strong>Jenis Kelamin:</strong>
                            <?= Html::encode($model->gender ?: '-') ?>
                        </div>
                        <div class="col-md-6 mb-2"><strong>Tempat Lahir:</strong>
                            <?= Html::encode($model->birth_place ?: '-') ?>
                        </div>
                        <div class="col-md-6 mb-2"><strong>Tanggal Lahir:</strong>
                            <?= $model->birth_date ? $formatter->asDate($model->birth_date, 'php:d F Y') : '-' ?>
                        </div>
                        <div class="col-md-6 mb-2"><strong>Status Pernikahan:</strong>
                            <?= Html::encode($model->marital_status ?: '-') ?>
                        </div>
                        <div class="col-md-6 mb-2"><strong>Agama:</strong> <?= Html::encode($model->religion ?: '-') ?>
                        </div>
                        <div class="col-md-6 mb-2"><strong>Pendidikan Terakhir:</strong>
                            <?= Html::encode($model->education ?: '-') ?>
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i
                            class="fas fa-map-marker-alt text-primary me-2"></i> Alamat</h6>
                    <div class="row mb-4">
                        <div class="col-12 mb-2"><strong>Alamat Sesuai KTP:</strong>
                            <?= nl2br(Html::encode($model->ktp_address ?: '-')) ?>
                        </div>
                        <div class="col-12 mb-2"><strong>Domisili Sama dengan KTP:</strong>
                            <?= $model->is_domicile_same_ktp ? 'Ya' : 'Tidak' ?>
                        </div>
                        <?php if (!$model->is_domicile_same_ktp): ?>
                            <div class="col-12 mb-2"><strong>Alamat Domisili:</strong>
                                <?= nl2br(Html::encode($model->domicile_address ?: '-')) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i
                            class="fas fa-phone-alt text-primary me-2"></i> Kontak Darurat</h6>
                    <div class="row">
                        <div class="col-md-4 mb-2"><strong>Nama:</strong>
                            <?= Html::encode($model->emergency_contact_name ?: '-') ?>
                        </div>
                        <div class="col-md-4 mb-2"><strong>Hubungan:</strong>
                            <?= Html::encode($model->emergency_contact_relation ?: '-') ?>
                        </div>
                        <div class="col-md-4 mb-2"><strong>No. Telepon:</strong>
                            <?= Html::encode($model->emergency_contact_phone ?: '-') ?>
                        </div>
                    </div>

                </div>

                <!-- TAB 2: PEKERJAAN -->
                <div class="tab-pane fade" id="tab-pekerjaan-pane" role="tabpanel">

                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i
                            class="fas fa-briefcase text-primary me-2"></i> Informasi Pekerjaan</h6>
                    <div class="row mb-4">
                        <div class="col-md-6 mb-2"><strong>Kode Karyawan:</strong>
                            <?= Html::encode($model->employee_code ?: '-') ?>
                        </div>
                        <div class="col-md-6 mb-2"><strong>Status Kepegawaian:</strong>
                            <?= Html::encode($model->employment_status ?: '-') ?>
                        </div>
                        <div class="col-md-6 mb-2"><strong>Departemen:</strong>
                            <?= Html::encode($model->department->name ?? '-') ?>
                        </div>
                        <div class="col-md-6 mb-2"><strong>Jabatan:</strong>
                            <?= Html::encode($model->position->name ?? '-') ?>
                        </div>
                        <div class="col-md-6 mb-2"><strong>Atasan Langsung:</strong>
                            <?= Html::encode($model->manager->name ?? '-') ?>
                        </div>
                        <div class="col-md-6 mb-2"><strong>Lokasi / Cabang:</strong>
                            <?= Html::encode($model->work_location ?: '-') ?>
                        </div>
                        <div class="col-md-6 mb-2"><strong>Tanggal Bergabung:</strong>
                            <?= $model->join_date ? $formatter->asDate($model->join_date, 'php:d F Y') : '-' ?>
                        </div>
                        <?php if (!empty($model->contract_end_date)): ?>
                            <div class="col-md-6 mb-2"><strong>Tanggal Akhir Kontrak:</strong>
                                <?= $formatter->asDate($model->contract_end_date, 'php:d F Y') ?>
                            </div>
                        <?php endif; ?>
                        <div class="col-md-6 mb-2"><strong>Mulai Hak Cuti:</strong>
                            <?= $model->leave_entitlement_date ? $formatter->asDate($model->leave_entitlement_date, 'php:d F Y') : '-' ?>
                        </div>
                        <div class="col-md-6 mb-2"><strong>Status Akun:</strong>
                            <?= GeneralHelper::textLabel($model->status ? 'Active' : 'Inactive', $model->status) ?>
                        </div>
                    </div>

                    <?php if ((int) $model->status === 0 || !empty($model->resign_date)): ?>
                        <h6 class="fw-bold text-danger border-bottom pb-2 mb-3"><i class="fas fa-user-minus me-2"></i>
                            Pengunduran Diri</h6>
                        <div class="row mb-4">
                            <div class="col-md-6 mb-2"><strong>Tanggal Keluar:</strong>
                                <?= $model->resign_date ? $formatter->asDate($model->resign_date, 'php:d F Y') : '-' ?>
                            </div>
                            <div class="col-md-6 mb-2"><strong>Alasan Keluar:</strong>
                                <?= Html::encode($model->resign_reason ?: '-') ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i
                            class="fas fa-calendar-check text-primary me-2"></i> Jatah Cuti</h6>
                    <div class="row">
                        <div class="col-md-6 mb-2"><strong>Jatah Cuti Per Tahun:</strong>
                            <?= (int) $model->annual_leave_quota ?> Hari
                        </div>
                        <div class="col-md-6 mb-2"><strong>Sisa Cuti:</strong> <?= (int) $model->remaining_leave ?> Hari
                        </div>
                    </div>

                </div>

                <!-- TAB 3: PAYROLL & PAJAK -->
                <div class="tab-pane fade" id="tab-payroll-pane" role="tabpanel">

                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i
                            class="fas fa-money-bill-wave text-primary me-2"></i> Gaji &amp; Tunjangan</h6>
                    <div class="row mb-4">
                        <div class="col-md-6 mb-2"><strong>Gaji Pokok:</strong>
                            <?= $formatter->asCurrency($model->basic_salary ?: 0, 'IDR') ?>
                        </div>
                        <div class="col-md-6 mb-2"><strong>Rate Per Jam:</strong>
                            <?= $formatter->asCurrency($model->hourly_rate ?: 0, 'IDR') ?>
                        </div>
                        <?php if (!empty($companyAllowances)): ?>
                            <?php foreach ($companyAllowances as $al): ?>
                                <div class="col-md-6 mb-2">
                                    <strong><?= Html::encode($al['name']) ?>:</strong>
                                    <?= $formatter->asCurrency($model->allowance_items[$al['uuid']] ?? 0, 'IDR') ?>
                                    <?php if ($al['is_fixed'] == '1'): ?>
                                        <span class="badge bg-light text-primary border ms-1">Tetap</span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i
                            class="fas fa-file-invoice-dollar text-primary me-2"></i> Pajak</h6>
                    <div class="row mb-4">
                        <div class="col-md-4 mb-2"><strong>NPWP:</strong> <?= Html::encode($model->npwp ?: '-') ?></div>
                        <div class="col-md-4 mb-2"><strong>Status PTKP:</strong>
                            <?= Html::encode($model->ptkp ?: '-') ?>
                        </div>
                        <div class="col-md-4 mb-2"><strong>Metode Pajak:</strong>
                            <?= Html::encode($model->tax_method ?: 'Gross') ?>
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i
                            class="fas fa-university text-primary me-2"></i> Rekening Bank</h6>
                    <div class="row">
                        <div class="col-md-4 mb-2"><strong>Nama Bank:</strong>
                            <?= Html::encode($model->bank_name ?: '-') ?>
                        </div>
                        <div class="col-md-4 mb-2"><strong>Nomor Rekening:</strong>
                            <?= Html::encode($model->bank_account_number ?: '-') ?>
                        </div>
                        <div class="col-md-4 mb-2"><strong>Atas Nama:</strong>
                            <?= Html::encode($model->bank_account_holder ?: '-') ?>
                        </div>
                    </div>

                </div>

                <!-- TAB 4: BPJS -->
                <div class="tab-pane fade" id="tab-bpjs-pane" role="tabpanel">

                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i
                            class="fas fa-id-badge text-primary me-2"></i> Nomor Kepesertaan</h6>
                    <div class="row mb-4">
                        <div class="col-md-6 mb-2"><strong>No. BPJS Kesehatan:</strong>
                            <?= Html::encode($model->bpjs_kesehatan_number ?: '-') ?>
                        </div>
                        <div class="col-md-6 mb-2"><strong>No. BPJS Ketenagakerjaan:</strong>
                            <?= Html::encode($model->bpjs_ketenagakerjaan_number ?: '-') ?>
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i
                            class="fas fa-shield-alt text-primary me-2"></i> Program BPJS yang Diikuti</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div
                                class="p-3 border rounded-3 bg-light d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>BPJS Kesehatan</strong>
                                    <small class="d-block text-muted">Iuran perusahaan + potongan karyawan</small>
                                </div>
                                <span class="badge <?= $model->bpjs_kesehatan ? 'bg-success' : 'bg-secondary' ?> fs-6">
                                    <?= $model->bpjs_kesehatan ? 'Aktif' : 'Tidak Aktif' ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div
                                class="p-3 border rounded-3 bg-light d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>JHT (Jaminan Hari Tua)</strong>
                                    <small class="d-block text-muted">Tabungan hari tua BPJS Ketenagakerjaan</small>
                                </div>
                                <span class="badge <?= $model->bpjs_jht ? 'bg-success' : 'bg-secondary' ?> fs-6">
                                    <?= $model->bpjs_jht ? 'Aktif' : 'Tidak Aktif' ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div
                                class="p-3 border rounded-3 bg-light d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>JP (Jaminan Pensiun)</strong>
                                    <small class="d-block text-muted">Pensiun bulanan saat hari tua</small>
                                </div>
                                <span class="badge <?= $model->bpjs_jp ? 'bg-success' : 'bg-secondary' ?> fs-6">
                                    <?= $model->bpjs_jp ? 'Aktif' : 'Tidak Aktif' ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div
                                class="p-3 border rounded-3 bg-light d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>JKK (Jaminan Kecelakaan Kerja)</strong>
                                    <small class="d-block text-muted">Perlindungan risiko kecelakaan kerja</small>
                                </div>
                                <span class="badge <?= $model->bpjs_jkk ? 'bg-success' : 'bg-secondary' ?> fs-6">
                                    <?= $model->bpjs_jkk ? 'Aktif' : 'Tidak Aktif' ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div
                                class="p-3 border rounded-3 bg-light d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>JKM (Jaminan Kematian)</strong>
                                    <small class="d-block text-muted">Santunan kematian bukan akibat kecelakaan
                                        kerja</small>
                                </div>
                                <span class="badge <?= $model->bpjs_jkm ? 'bg-success' : 'bg-secondary' ?> fs-6">
                                    <?= $model->bpjs_jkm ? 'Aktif' : 'Tidak Aktif' ?>
                                </span>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

</div>