<?php

use app\helpers\GeneralHelper;
use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var app\models\master\Account $model */
/** @var array $companyAllowances */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Data User', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$formatter = Yii::$app->formatter;

// Hitung usia
$ageText = null;
if (!empty($model->birth_date)) {
    try {
        $bdate = new \DateTime($model->birth_date);
        $today = new \DateTime();
        $ageText = $today->diff($bdate)->y . ' Tahun';
    } catch (\Exception $e) {
        $ageText = null;
    }
}

// Hitung masa kerja
$tenureText = null;
if (!empty($model->join_date)) {
    try {
        $jdate = new \DateTime($model->join_date);
        $today = new \DateTime();
        $diff = $jdate->diff($today);
        $parts = [];
        if ($diff->y > 0) $parts[] = $diff->y . ' Thn';
        if ($diff->m > 0) $parts[] = $diff->m . ' Bln';
        if (empty($parts)) $parts[] = $diff->d . ' Hari';
        $tenureText = implode(' ', $parts);
    } catch (\Exception $e) {
        $tenureText = null;
    }
}

// Inisial avatar jika tidak ada foto
$nameTrim = trim($model->name ?? '');
$nameWords = preg_split('/\s+/', $nameTrim);
$initials = '';
if (!empty($nameWords[0])) {
    $initials .= mb_strtoupper(mb_substr($nameWords[0], 0, 1));
}
if (count($nameWords) > 1 && !empty($nameWords[1])) {
    $initials .= mb_strtoupper(mb_substr($nameWords[1], 0, 1));
}
if ($initials === '') {
    $initials = 'U';
}

// Format WhatsApp
$cleanPhone = preg_replace('/[^0-9]/', '', $model->phone ?? '');
$waPhone = '';
if (!empty($cleanPhone)) {
    $waPhone = str_starts_with($cleanPhone, '0') ? '62' . substr($cleanPhone, 1) : $cleanPhone;
}

// Hitung cuti
$annualQuota = (int) ($model->annual_leave_quota ?? 12);
$remainingLeave = (int) ($model->remaining_leave ?? 0);
$usedLeave = max(0, $annualQuota - $remainingLeave);
$leavePercent = $annualQuota > 0 ? min(100, max(0, round(($remainingLeave / $annualQuota) * 100))) : 0;
?>

<style>
    .user-profile-hero {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05);
        padding: 24px;
        position: relative;
        overflow: hidden;
    }
    .user-profile-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, #3b82f6 0%, #6366f1 50%, #8b5cf6 100%);
    }
    .avatar-wrapper {
        position: relative;
        width: 88px;
        height: 88px;
        flex-shrink: 0;
    }
    .avatar-img {
        width: 88px;
        height: 88px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #ffffff;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
    }
    .avatar-initials {
        width: 88px;
        height: 88px;
        border-radius: 50%;
        background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
        color: #ffffff;
        font-size: 28px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid #ffffff;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.25);
        letter-spacing: 0.5px;
    }
    .status-dot {
        position: absolute;
        bottom: 2px;
        right: 2px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        border: 3px solid #ffffff;
    }
    .status-dot.active {
        background-color: #10b981;
    }
    .status-dot.inactive {
        background-color: #ef4444;
    }

    .meta-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #475569;
        font-size: 0.82rem;
        padding: 4px 10px;
        border-radius: 8px;
        font-weight: 500;
    }
    .meta-chip a {
        color: inherit;
        text-decoration: none;
    }
    .meta-chip a:hover {
        color: #2563eb;
    }

    .stat-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 14px;
        min-width: 120px;
        flex: 1 1 120px;
        text-align: center;
    }
    .stat-box-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.5px;
        margin-bottom: 2px;
    }
    .stat-box-value {
        font-size: 1.15rem;
        font-weight: 700;
        color: #0f172a;
    }

    .nav-tabs-modern {
        border-bottom: 2px solid #e2e8f0;
        gap: 6px;
        padding: 0 16px;
        background: #ffffff;
        flex-wrap: nowrap;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .nav-tabs-modern::-webkit-scrollbar {
        height: 4px;
    }
    .nav-tabs-modern::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    .nav-tabs-modern .nav-item {
        flex-shrink: 0;
    }
    .nav-tabs-modern .nav-link {
        border: none;
        background: transparent;
        color: #64748b;
        font-weight: 600;
        font-size: 0.92rem;
        padding: 14px 18px;
        border-bottom: 3px solid transparent;
        border-radius: 0;
        margin-bottom: -2px;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        white-space: nowrap;
    }
    .nav-tabs-modern .nav-link:hover {
        color: #2563eb;
        background-color: transparent;
    }
    .nav-tabs-modern .nav-link.active {
        color: #2563eb;
        border-bottom-color: #2563eb;
        background-color: transparent;
    }

    .section-card {
        background: #ffffff;
        border: 1px solid #eef2f6;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    .section-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 18px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f1f5f9;
    }
    .section-header-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: #eff6ff;
        color: #2563eb;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
    }
    .section-title {
        font-size: 0.98rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }

    .field-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px 14px;
        height: 100%;
        transition: all 0.2s ease;
    }
    .field-card:hover {
        background: #ffffff;
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px -2px rgba(0, 0, 0, 0.05);
    }
    .field-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .field-value {
        font-size: 0.92rem;
        font-weight: 600;
        color: #1e293b;
        word-break: break-word;
    }

    .btn-copy {
        background: none;
        border: none;
        color: #94a3b8;
        padding: 0 4px;
        font-size: 0.8rem;
        cursor: pointer;
        transition: color 0.15s;
    }
    .btn-copy:hover {
        color: #2563eb;
    }

    .bpjs-badge-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px;
        transition: all 0.2s ease;
        height: 100%;
    }
    .bpjs-badge-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0,0,0,0.04);
        transform: translateY(-2px);
    }
    .bpjs-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .bank-virtual-card {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        color: #ffffff;
        border-radius: 16px;
        padding: 24px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.2);
    }
    .bank-virtual-card::after {
        content: '';
        position: absolute;
        top: -40px;
        right: -40px;
        width: 140px;
        height: 140px;
        background: rgba(255,255,255,0.06);
        border-radius: 50%;
    }

    .salary-highlight-card {
        background: linear-gradient(135deg, #eff6ff 0%, #e0e7ff 100%);
        border: 1px solid #bfdbfe;
        border-radius: 14px;
        padding: 20px;
    }
</style>

<div class="user-view">

    <!-- Top Action Toolbar -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Detail Karyawan</h4>
            <div class="text-muted small">Kelola informasi data diri, jabatan, payroll, dan jaminan sosial.</div>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2 w-100 w-sm-auto justify-content-start justify-content-sm-end">
            <?= Html::a('<i class="fas fa-arrow-left me-1"></i> Kembali', ['index'], [
                'class' => 'btn btn-outline-secondary px-3 shadow-sm',
                'title' => 'Kembali ke daftar karyawan'
            ]) ?>
            <?= Html::a('<i class="fas fa-user-edit me-1"></i> Ubah Data', ['process', 'id' => $model->uuid], [
                'class' => 'btn btn-primary px-3 shadow-sm',
                'title' => 'Perbarui data karyawan'
            ]) ?>
            <?= Html::a('<i class="fas fa-trash-alt me-1"></i> Hapus', ['delete', 'id' => $model->uuid], [
                'class' => 'btn btn-outline-danger px-3 shadow-sm',
                'title' => 'Hapus karyawan ini',
                'data' => [
                    'confirm' => 'Apakah Anda yakin ingin menghapus data karyawan ' . Html::encode($model->name) . '?',
                    'method' => 'post',
                ],
            ]) ?>
        </div>
    </div>

    <!-- Executive Profile Hero Card -->
    <div class="user-profile-hero mb-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-4">
            
            <!-- Left: Avatar & Main Identity -->
            <div class="d-flex flex-column flex-sm-row align-items-center align-items-sm-start text-center text-sm-start gap-3">
                <div class="avatar-wrapper">
                    <?php if (!empty($model->photo)): ?>
                        <img src="<?= Yii::getAlias('@web/' . $model->photo) ?>" alt="<?= Html::encode($model->name) ?>" class="avatar-img">
                    <?php else: ?>
                        <div class="avatar-initials">
                            <?= Html::encode($initials) ?>
                        </div>
                    <?php endif; ?>
                    <span class="status-dot <?= $model->status ? 'active' : 'inactive' ?>" title="<?= $model->status ? 'Akun Aktif' : 'Akun Nonaktif' ?>"></span>
                </div>

                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                        <h4 class="fw-bold text-dark mb-0"><?= Html::encode($model->name) ?></h4>
                        <?php if ($model->status): ?>
                            <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-2 py-1 small">
                                <i class="fas fa-check-circle me-1"></i> Aktif
                            </span>
                        <?php else: ?>
                            <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 small">
                                <i class="fas fa-times-circle me-1"></i> Nonaktif
                            </span>
                        <?php endif; ?>

                        <?php if (!empty($model->employment_status)): ?>
                            <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 small">
                                <?= Html::encode($model->employment_status) ?>
                            </span>
                        <?php endif; ?>

                        <?php if (!empty($model->role->item_name)): ?>
                            <span class="badge rounded-pill bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 small">
                                <i class="fas fa-user-shield me-1"></i> <?= Html::encode($model->role->item_name) ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Subtitle / Meta Badges -->
                    <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
                        <?php if (!empty($model->employee_code)): ?>
                            <div class="meta-chip">
                                <i class="fas fa-id-badge text-primary"></i>
                                <span><?= Html::encode($model->employee_code) ?></span>
                                <button type="button" class="btn-copy" data-copy="<?= Html::encode($model->employee_code) ?>" title="Salin Kode Karyawan">
                                    <i class="far fa-copy"></i>
                                </button>
                            </div>
                        <?php endif; ?>

                        <div class="meta-chip">
                            <i class="fas fa-briefcase text-primary"></i>
                            <span><?= Html::encode($model->position->name ?? 'Tanpa Jabatan') ?></span>
                            <span class="text-muted">•</span>
                            <span><?= Html::encode($model->department->name ?? 'Tanpa Departemen') ?></span>
                        </div>

                        <?php if (!empty($model->company->name)): ?>
                            <div class="meta-chip">
                                <i class="fas fa-building text-primary"></i>
                                <span><?= Html::encode($model->company->name) ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($model->email)): ?>
                            <div class="meta-chip">
                                <i class="fas fa-envelope text-primary"></i>
                                <a href="mailto:<?= Html::encode($model->email) ?>"><?= Html::encode($model->email) ?></a>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($model->phone)): ?>
                            <div class="meta-chip">
                                <i class="fab fa-whatsapp text-success"></i>
                                <?php if ($waPhone): ?>
                                    <a href="https://wa.me/<?= $waPhone ?>" target="_blank" rel="noopener noreferrer"><?= Html::encode($model->phone) ?></a>
                                <?php else: ?>
                                    <span><?= Html::encode($model->phone) ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right: Highlight Metrics -->
            <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-2 align-self-stretch align-self-md-center flex-wrap">
                <div class="stat-box">
                    <div class="stat-box-label"><i class="fas fa-calendar-day me-1"></i> Masa Kerja</div>
                    <div class="stat-box-value"><?= $tenureText ?: '-' ?></div>
                    <small class="text-muted d-block" style="font-size: 0.72rem;">
                        Gabung: <?= $model->join_date ? $formatter->asDate($model->join_date, 'php:d M Y') : '-' ?>
                    </small>
                </div>
                <div class="stat-box">
                    <div class="stat-box-label"><i class="fas fa-calendar-check me-1"></i> Sisa Cuti</div>
                    <div class="stat-box-value text-primary"><?= $remainingLeave ?> <span class="fs-6 text-muted fw-normal">/ <?= $annualQuota ?></span></div>
                    <small class="text-muted d-block" style="font-size: 0.72rem;">Hari per tahun</small>
                </div>
                <?php if (!empty($model->basic_salary)): ?>
                <div class="stat-box d-none d-lg-block">
                    <div class="stat-box-label"><i class="fas fa-receipt me-1"></i> Pajak & PTKP</div>
                    <div class="stat-box-value fs-6"><?= Html::encode($model->ptkp ?: 'TK/0') ?></div>
                    <small class="text-muted d-block" style="font-size: 0.72rem;"><?= Html::encode($model->tax_method ?: 'Gross') ?></small>
                </div>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <!-- Tabs Container -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        
        <!-- Tab Navigation Header -->
        <div class="bg-white border-bottom p-0">
            <ul class="nav nav-tabs-modern" id="viewUserTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-pribadi-btn" data-bs-toggle="tab" data-bs-target="#tab-pribadi-pane" type="button" role="tab">
                        <i class="fas fa-user-circle me-2 text-primary"></i> Data Pribadi
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-pekerjaan-btn" data-bs-toggle="tab" data-bs-target="#tab-pekerjaan-pane" type="button" role="tab">
                        <i class="fas fa-briefcase me-2 text-primary"></i> Pekerjaan &amp; Karir
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-payroll-btn" data-bs-toggle="tab" data-bs-target="#tab-payroll-pane" type="button" role="tab">
                        <i class="fas fa-money-bill-wave me-2 text-primary"></i> Payroll &amp; Pajak
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-bpjs-btn" data-bs-toggle="tab" data-bs-target="#tab-bpjs-pane" type="button" role="tab">
                        <i class="fas fa-shield-alt me-2 text-primary"></i> BPJS &amp; Asuransi
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-4 bg-light-subtle">
            <div class="tab-content" id="viewUserTabsContent">

                <!-- ========================================== -->
                <!-- TAB 1: DATA PRIBADI                       -->
                <!-- ========================================== -->
                <div class="tab-pane fade show active" id="tab-pribadi-pane" role="tabpanel">

                    <!-- Section: Akun & Kontak -->
                    <div class="section-card">
                        <div class="section-header">
                            <div class="section-header-icon"><i class="fas fa-user"></i></div>
                            <div>
                                <h6 class="section-title">Informasi Akun &amp; Kontak</h6>
                                <small class="text-muted">Data kredensial dan akses komunikasi karyawan</small>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6 col-lg-3">
                                <div class="field-card">
                                    <div class="field-label">Nama Lengkap</div>
                                    <div class="field-value"><?= Html::encode($model->name) ?></div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="field-card">
                                    <div class="field-label">
                                        <span>Email Akun</span>
                                        <?php if (!empty($model->email)): ?>
                                            <button type="button" class="btn-copy" data-copy="<?= Html::encode($model->email) ?>" title="Salin Email">
                                                <i class="far fa-copy"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                    <div class="field-value text-truncate">
                                        <a href="mailto:<?= Html::encode($model->email) ?>" class="text-decoration-none text-dark">
                                            <?= Html::encode($model->email) ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="field-card">
                                    <div class="field-label">
                                        <span>No. Telepon / WA</span>
                                        <?php if (!empty($model->phone)): ?>
                                            <button type="button" class="btn-copy" data-copy="<?= Html::encode($model->phone) ?>" title="Salin Nomor">
                                                <i class="far fa-copy"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                    <div class="field-value d-flex align-items-center justify-content-between">
                                        <span><?= Html::encode($model->phone ?: '-') ?></span>
                                        <?php if ($waPhone): ?>
                                            <a href="https://wa.me/<?= $waPhone ?>" target="_blank" class="btn btn-sm btn-outline-success py-0 px-2" style="font-size: 0.75rem;">
                                                <i class="fab fa-whatsapp"></i> Chat
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="field-card">
                                    <div class="field-label">Role Akses</div>
                                    <div class="field-value">
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                            <i class="fas fa-user-shield me-1"></i> <?= Html::encode($model->role->item_name ?? 'Karyawan') ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Identitas Kependudukan -->
                    <div class="section-card">
                        <div class="section-header">
                            <div class="section-header-icon"><i class="fas fa-id-card"></i></div>
                            <div>
                                <h6 class="section-title">Identitas Kependudukan</h6>
                                <small class="text-muted">Data kependudukan resmi berdasarkan KTP</small>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6 col-lg-4">
                                <div class="field-card">
                                    <div class="field-label">
                                        <span>NIK KTP (16 Digit)</span>
                                        <?php if (!empty($model->nik)): ?>
                                            <button type="button" class="btn-copy" data-copy="<?= Html::encode($model->nik) ?>" title="Salin NIK">
                                                <i class="far fa-copy"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                    <div class="field-value font-monospace text-primary">
                                        <?= Html::encode($model->nik ?: '-') ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="field-card">
                                    <div class="field-label">Jenis Kelamin</div>
                                    <div class="field-value">
                                        <?php if ($model->gender === 'Laki-laki' || $model->gender === 'L'): ?>
                                            <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-2 py-1">
                                                <i class="fas fa-mars me-1"></i> Laki-laki
                                            </span>
                                        <?php elseif ($model->gender === 'Perempuan' || $model->gender === 'P'): ?>
                                            <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle px-2 py-1">
                                                <i class="fas fa-venus me-1"></i> Perempuan
                                            </span>
                                        <?php else: ?>
                                            <?= Html::encode($model->gender ?: '-') ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="field-card">
                                    <div class="field-label">Tempat, Tanggal Lahir</div>
                                    <div class="field-value">
                                        <?= Html::encode($model->birth_place ?: '-') ?>,
                                        <?= $model->birth_date ? $formatter->asDate($model->birth_date, 'php:d F Y') : '-' ?>
                                        <?php if ($ageText): ?>
                                            <span class="badge bg-secondary-subtle text-secondary small ms-1"><?= $ageText ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="field-card">
                                    <div class="field-label">Status Pernikahan</div>
                                    <div class="field-value"><?= Html::encode($model->marital_status ?: '-') ?></div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="field-card">
                                    <div class="field-label">Agama</div>
                                    <div class="field-value"><?= Html::encode($model->religion ?: '-') ?></div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="field-card">
                                    <div class="field-label">Pendidikan Terakhir</div>
                                    <div class="field-value"><?= Html::encode($model->education ?: '-') ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Alamat & Domisili -->
                    <div class="section-card">
                        <div class="section-header">
                            <div class="section-header-icon"><i class="fas fa-map-marked-alt"></i></div>
                            <div>
                                <h6 class="section-title">Alamat &amp; Domisili</h6>
                                <small class="text-muted">Informasi tempat tinggal sesuai KTP dan tempat tinggal saat ini</small>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-lg-6">
                                <div class="field-card">
                                    <div class="field-label">
                                        <span><i class="fas fa-home text-muted me-1"></i> Alamat Sesuai KTP</span>
                                    </div>
                                    <div class="field-value fw-normal text-secondary">
                                        <?= $model->ktp_address ? nl2br(Html::encode($model->ktp_address)) : '<span class="text-muted fst-italic">Belum diisi</span>' ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="field-card">
                                    <div class="field-label d-flex justify-content-between">
                                        <span><i class="fas fa-map-pin text-muted me-1"></i> Alamat Domisili</span>
                                        <?php if ($model->is_domicile_same_ktp): ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                <i class="fas fa-check me-1"></i> Sama dengan KTP
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                                Berbeda dengan KTP
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="field-value fw-normal text-secondary">
                                        <?php if ($model->is_domicile_same_ktp): ?>
                                            <?= $model->ktp_address ? nl2br(Html::encode($model->ktp_address)) : '<span class="text-muted fst-italic">Sama dengan KTP</span>' ?>
                                        <?php else: ?>
                                            <?= $model->domicile_address ? nl2br(Html::encode($model->domicile_address)) : '<span class="text-muted fst-italic">Belum diisi</span>' ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Kontak Darurat -->
                    <div class="section-card mb-0">
                        <div class="section-header">
                            <div class="section-header-icon bg-warning-subtle text-warning"><i class="fas fa-phone-alt"></i></div>
                            <div>
                                <h6 class="section-title">Kontak Darurat (Emergency Contact)</h6>
                                <small class="text-muted">Pihak yang dapat dihubungi saat situasi darurat</small>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="field-card">
                                    <div class="field-label">Nama Kontak</div>
                                    <div class="field-value"><?= Html::encode($model->emergency_contact_name ?: '-') ?></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="field-card">
                                    <div class="field-label">Hubungan / Relasi</div>
                                    <div class="field-value">
                                        <?php if (!empty($model->emergency_contact_relation)): ?>
                                            <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">
                                                <?= Html::encode($model->emergency_contact_relation) ?>
                                            </span>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="field-card">
                                    <div class="field-label">
                                        <span>Nomor Telepon Darurat</span>
                                        <?php if (!empty($model->emergency_contact_phone)): ?>
                                            <button type="button" class="btn-copy" data-copy="<?= Html::encode($model->emergency_contact_phone) ?>" title="Salin Nomor">
                                                <i class="far fa-copy"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                    <div class="field-value d-flex align-items-center justify-content-between">
                                        <span><?= Html::encode($model->emergency_contact_phone ?: '-') ?></span>
                                        <?php if (!empty($model->emergency_contact_phone)): ?>
                                            <a href="tel:<?= Html::encode($model->emergency_contact_phone) ?>" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 0.75rem;">
                                                <i class="fas fa-phone"></i> Hubungi
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- ========================================== -->
                <!-- TAB 2: PEKERJAAN & KARIR                  -->
                <!-- ========================================== -->
                <div class="tab-pane fade" id="tab-pekerjaan-pane" role="tabpanel">

                    <!-- Section: Penempatan & Struktur Organisasi -->
                    <div class="section-card">
                        <div class="section-header">
                            <div class="section-header-icon"><i class="fas fa-sitemap"></i></div>
                            <div>
                                <h6 class="section-title">Penempatan &amp; Posisi Organisasi</h6>
                                <small class="text-muted">Informasi struktur jabatan dan tempat bertugas</small>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6 col-lg-3">
                                <div class="field-card">
                                    <div class="field-label">
                                        <span>Kode Karyawan (NIP)</span>
                                        <?php if (!empty($model->employee_code)): ?>
                                            <button type="button" class="btn-copy" data-copy="<?= Html::encode($model->employee_code) ?>" title="Salin Kode">
                                                <i class="far fa-copy"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                    <div class="field-value font-monospace text-primary"><?= Html::encode($model->employee_code ?: '-') ?></div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="field-card">
                                    <div class="field-label">Status Kepegawaian</div>
                                    <div class="field-value">
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                            <?= Html::encode($model->employment_status ?: '-') ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="field-card">
                                    <div class="field-label">Perusahaan</div>
                                    <div class="field-value"><?= Html::encode($model->company->name ?? '-') ?></div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="field-card">
                                    <div class="field-label">Departemen</div>
                                    <div class="field-value"><?= Html::encode($model->department->name ?? '-') ?></div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="field-card">
                                    <div class="field-label">Jabatan</div>
                                    <div class="field-value fw-bold text-dark"><?= Html::encode($model->position->name ?? '-') ?></div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="field-card">
                                    <div class="field-label">Atasan Langsung</div>
                                    <div class="field-value d-flex align-items-center gap-2">
                                        <i class="fas fa-user-tie text-muted"></i>
                                        <span><?= Html::encode($model->manager->name ?? '-') ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="field-card">
                                    <div class="field-label">Lokasi / Cabang Kerja</div>
                                    <div class="field-value">
                                        <i class="fas fa-location-dot text-danger me-1"></i>
                                        <?= Html::encode($model->work_location ?: '-') ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="field-card">
                                    <div class="field-label">Status Akun</div>
                                    <div class="field-value">
                                        <?= GeneralHelper::textLabel($model->status ? 'Active' : 'Inactive', $model->status) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Periode Kerja & Kontrak -->
                    <div class="section-card">
                        <div class="section-header">
                            <div class="section-header-icon"><i class="fas fa-calendar-alt"></i></div>
                            <div>
                                <h6 class="section-title">Periode Masa Kerja &amp; Kontrak</h6>
                                <small class="text-muted">Tanggal pengangkatan, durasi masa kerja, dan masa berlaku kontrak</small>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6 col-lg-3">
                                <div class="field-card">
                                    <div class="field-label">Tanggal Bergabung</div>
                                    <div class="field-value">
                                        <?= $model->join_date ? $formatter->asDate($model->join_date, 'php:d F Y') : '-' ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="field-card">
                                    <div class="field-label">Masa Kerja (Tenure)</div>
                                    <div class="field-value text-primary fw-bold">
                                        <?= $tenureText ?: '-' ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="field-card">
                                    <div class="field-label">Tanggal Akhir Kontrak</div>
                                    <div class="field-value">
                                        <?php if (!empty($model->contract_end_date)): ?>
                                            <span class="text-warning-emphasis fw-bold">
                                                <?= $formatter->asDate($model->contract_end_date, 'php:d F Y') ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">Tidak ada (Permanen)</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="field-card">
                                    <div class="field-label">Mulai Hak Cuti</div>
                                    <div class="field-value">
                                        <?= $model->leave_entitlement_date ? $formatter->asDate($model->leave_entitlement_date, 'php:d F Y') : '-' ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Jatah Cuti & Kuota -->
                    <div class="section-card">
                        <div class="section-header">
                            <div class="section-header-icon bg-success-subtle text-success"><i class="fas fa-calendar-check"></i></div>
                            <div>
                                <h6 class="section-title">Jatah &amp; Kuota Cuti Tahunan</h6>
                                <small class="text-muted">Alokasi cuti tahunan dan sisa hak cuti karyawan</small>
                            </div>
                        </div>
                        <div class="row align-items-center g-4">
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded-3 border text-center">
                                    <div class="text-muted small text-uppercase fw-bold">Total Kuota Cuti</div>
                                    <div class="fs-2 fw-bold text-dark my-1"><?= $annualQuota ?> <span class="fs-6 fw-normal text-muted">Hari</span></div>
                                    <small class="text-muted">Alokasi per tahun berjalan</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 bg-primary-subtle border border-primary-subtle rounded-3 text-center">
                                    <div class="text-primary small text-uppercase fw-bold">Sisa Cuti Tersedia</div>
                                    <div class="fs-2 fw-bold text-primary my-1"><?= $remainingLeave ?> <span class="fs-6 fw-normal text-primary">Hari</span></div>
                                    <small class="text-primary-emphasis">Dapat diajukan saat ini</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded-3 border text-center">
                                    <div class="text-muted small text-uppercase fw-bold">Cuti Terpakai</div>
                                    <div class="fs-2 fw-bold text-secondary my-1"><?= $usedLeave ?> <span class="fs-6 fw-normal text-muted">Hari</span></div>
                                    <small class="text-muted">Total hari yang telah diambil</small>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex justify-content-between small text-muted mb-1">
                                    <span>Tingkat Ketersediaan Cuti</span>
                                    <span class="fw-bold"><?= $leavePercent ?>% (<?= $remainingLeave ?> dari <?= $annualQuota ?> Hari)</span>
                                </div>
                                <div class="progress" style="height: 10px; border-radius: 99px;">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: <?= $leavePercent ?>%;" aria-valuenow="<?= $leavePercent ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Pengunduran Diri (Jika Inactive / Resign) -->
                    <?php if ((int) $model->status === 0 || !empty($model->resign_date)): ?>
                        <div class="section-card border-danger-subtle bg-danger-subtle bg-opacity-10 mb-0">
                            <div class="section-header border-danger-subtle">
                                <div class="section-header-icon bg-danger-subtle text-danger"><i class="fas fa-user-minus"></i></div>
                                <div>
                                    <h6 class="section-title text-danger">Informasi Pengunduran Diri (Resign)</h6>
                                    <small class="text-muted">Catatan terminasi atau pengunduran diri karyawan</small>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="field-card bg-white">
                                        <div class="field-label text-danger">Tanggal Berhenti / Resign</div>
                                        <div class="field-value text-danger fw-bold">
                                            <?= $model->resign_date ? $formatter->asDate($model->resign_date, 'php:d F Y') : '-' ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="field-card bg-white">
                                        <div class="field-label text-danger">Alasan Pengunduran Diri</div>
                                        <div class="field-value">
                                            <?= Html::encode($model->resign_reason ?: '-') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>

                <!-- ========================================== -->
                <!-- TAB 3: PAYROLL & PAJAK                     -->
                <!-- ========================================== -->
                <div class="tab-pane fade" id="tab-payroll-pane" role="tabpanel">

                    <!-- Section: Kompensasi & Gaji -->
                    <div class="section-card">
                        <div class="section-header">
                            <div class="section-header-icon"><i class="fas fa-money-bill-wave"></i></div>
                            <div>
                                <h6 class="section-title">Gaji Pokok &amp; Rate</h6>
                                <small class="text-muted">Struktur kompensasi dasar dan rate lembur/jam</small>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="salary-highlight-card d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-primary small text-uppercase fw-bold mb-1">Gaji Pokok (Basic Salary)</div>
                                        <div class="fs-3 fw-bold text-dark">
                                            <?= $formatter->asCurrency($model->basic_salary ?: 0, 'IDR') ?>
                                        </div>
                                        <small class="text-muted">Nominal dasar sebelum tunjangan &amp; potongan</small>
                                    </div>
                                    <div class="text-primary fs-1 opacity-50 pe-3">
                                        <i class="fas fa-wallet"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 bg-light d-flex align-items-center justify-content-between h-100">
                                    <div>
                                        <div class="text-muted small text-uppercase fw-bold mb-1">Rate Lembur / Jam</div>
                                        <div class="fs-3 fw-bold text-dark">
                                            <?= $formatter->asCurrency($model->hourly_rate ?: 0, 'IDR') ?>
                                        </div>
                                        <small class="text-muted">Rate acuan perhitungan lembur karyawan</small>
                                    </div>
                                    <div class="text-secondary fs-1 opacity-25 pe-3">
                                        <i class="fas fa-stopwatch"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tunjangan Perusahaan -->
                        <h6 class="fw-bold text-dark mb-3"><i class="fas fa-hand-holding-dollar text-primary me-2"></i> Tunjangan Perusahaan</h6>
                        <?php if (!empty($companyAllowances)): ?>
                            <div class="row g-3">
                                <?php foreach ($companyAllowances as $al): ?>
                                    <div class="col-md-6 col-lg-4">
                                        <div class="field-card">
                                            <div class="field-label d-flex justify-content-between">
                                                <span><?= Html::encode($al['name'] ?? '-') ?></span>
                                                <?php if (!empty($al['is_fixed']) && (string)$al['is_fixed'] === '1'): ?>
                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Tetap</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary-subtle text-secondary border">Variabel</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="field-value text-dark fw-bold fs-6">
                                                <?= $formatter->asCurrency($model->allowance_items[$al['uuid']] ?? 0, 'IDR') ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="p-3 border rounded-3 bg-light text-center text-muted small">
                                <i class="fas fa-info-circle me-1"></i> Belum ada data tunjangan terdaftar untuk karyawan ini.
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Section: Rekening Bank -->
                    <div class="section-card">
                        <div class="section-header">
                            <div class="section-header-icon"><i class="fas fa-university"></i></div>
                            <div>
                                <h6 class="section-title">Rekening Bank Penggajian</h6>
                                <small class="text-muted">Rekening tujuan transfer gaji bulanan</small>
                            </div>
                        </div>

                        <div class="row g-4 align-items-center">
                            <div class="col-lg-5">
                                <div class="bank-virtual-card">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <span class="fw-bold tracking-wider fs-6"><i class="fas fa-credit-card me-2"></i> REKENING PAYROLL</span>
                                        <span class="badge bg-white text-dark fw-bold px-2 py-1"><?= Html::encode($model->bank_name ?: 'BANK') ?></span>
                                    </div>
                                    <div class="font-monospace fs-4 fw-bold letter-spacing-2 mb-3">
                                        <?= Html::encode($model->bank_account_number ?: '•••• •••• ••••') ?>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-end">
                                        <div>
                                            <small class="text-white-50 text-uppercase d-block" style="font-size: 0.7rem;">Atas Nama</small>
                                            <span class="fw-bold"><?= Html::encode($model->bank_account_holder ?: $model->name) ?></span>
                                        </div>
                                        <?php if (!empty($model->bank_account_number)): ?>
                                            <button type="button" class="btn btn-sm btn-outline-light py-1 px-2 btn-copy" data-copy="<?= Html::encode($model->bank_account_number) ?>" title="Salin Rekening">
                                                <i class="far fa-copy me-1"></i> Salin
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="field-card">
                                            <div class="field-label">Nama Bank</div>
                                            <div class="field-value"><?= Html::encode($model->bank_name ?: '-') ?></div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="field-card">
                                            <div class="field-label">Nomor Rekening</div>
                                            <div class="field-value font-monospace text-primary fw-bold">
                                                <?= Html::encode($model->bank_account_number ?: '-') ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="field-card">
                                            <div class="field-label">Nama Pemilik Rekening</div>
                                            <div class="field-value"><?= Html::encode($model->bank_account_holder ?: '-') ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Perpajakan -->
                    <div class="section-card mb-0">
                        <div class="section-header">
                            <div class="section-header-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                            <div>
                                <h6 class="section-title">Perpajakan (PPh 21)</h6>
                                <small class="text-muted">Data nomor pokok wajib pajak dan kategori PTKP</small>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="field-card">
                                    <div class="field-label">
                                        <span>Nomor NPWP</span>
                                        <?php if (!empty($model->npwp)): ?>
                                            <button type="button" class="btn-copy" data-copy="<?= Html::encode($model->npwp) ?>" title="Salin NPWP">
                                                <i class="far fa-copy"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                    <div class="field-value font-monospace text-primary">
                                        <?= Html::encode($model->npwp ?: 'Tidak Ada (00.000.000.0-000.000)') ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="field-card">
                                    <div class="field-label">Kategori Status PTKP</div>
                                    <div class="field-value">
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                            <?= Html::encode($model->ptkp ?: 'TK/0') ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="field-card">
                                    <div class="field-label">Metode Perhitungan Pajak</div>
                                    <div class="field-value">
                                        <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">
                                            <?= Html::encode($model->tax_method ?: 'Gross') ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- ========================================== -->
                <!-- TAB 4: BPJS & ASURANSI                     -->
                <!-- ========================================== -->
                <div class="tab-pane fade" id="tab-bpjs-pane" role="tabpanel">

                    <!-- Section: Nomor Kartu Kepesertaan -->
                    <div class="section-card">
                        <div class="section-header">
                            <div class="section-header-icon"><i class="fas fa-id-card-clip"></i></div>
                            <div>
                                <h6 class="section-title">Nomor Kartu Kepesertaan BPJS</h6>
                                <small class="text-muted">Nomor identitas terdaftar pada program jaminan kesehatan &amp; ketenagakerjaan</small>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="field-card p-3">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="fw-bold text-dark d-flex align-items-center gap-2">
                                            <i class="fas fa-heart-pulse text-success fs-5"></i> BPJS Kesehatan
                                        </span>
                                        <?php if ($model->bpjs_kesehatan): ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary-subtle text-secondary border">Nonaktif</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="font-monospace fs-5 fw-bold text-primary">
                                            <?= Html::encode($model->bpjs_kesehatan_number ?: '-') ?>
                                        </span>
                                        <?php if (!empty($model->bpjs_kesehatan_number)): ?>
                                            <button type="button" class="btn btn-sm btn-outline-secondary btn-copy" data-copy="<?= Html::encode($model->bpjs_kesehatan_number) ?>" title="Salin Nomor">
                                                <i class="far fa-copy"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="field-card p-3">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="fw-bold text-dark d-flex align-items-center gap-2">
                                            <i class="fas fa-shield-alt text-primary fs-5"></i> BPJS Ketenagakerjaan
                                        </span>
                                        <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle">Ketenagakerjaan</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="font-monospace fs-5 fw-bold text-primary">
                                            <?= Html::encode($model->bpjs_ketenagakerjaan_number ?: '-') ?>
                                        </span>
                                        <?php if (!empty($model->bpjs_ketenagakerjaan_number)): ?>
                                            <button type="button" class="btn btn-sm btn-outline-secondary btn-copy" data-copy="<?= Html::encode($model->bpjs_ketenagakerjaan_number) ?>" title="Salin Nomor">
                                                <i class="far fa-copy"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Program Jaminan Sosial -->
                    <div class="section-card mb-0">
                        <div class="section-header">
                            <div class="section-header-icon"><i class="fas fa-shield-heart"></i></div>
                            <div>
                                <h6 class="section-title">Status Partisipasi Program BPJS</h6>
                                <small class="text-muted">Daftar program jaminan sosial yang diikutsertakan bagi karyawan ini</small>
                            </div>
                        </div>

                        <div class="row g-3">
                            
                            <!-- BPJS Kesehatan -->
                            <div class="col-md-6 col-lg-4">
                                <div class="bpjs-badge-card d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <div class="bpjs-icon-box bg-success-subtle text-success">
                                                <i class="fas fa-heart-pulse"></i>
                                            </div>
                                            <?php if ($model->bpjs_kesehatan): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                                    <i class="fas fa-check-circle me-1"></i> Aktif
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">
                                                    <i class="fas fa-minus-circle me-1"></i> Tidak Aktif
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">BPJS Kesehatan</h6>
                                        <p class="text-muted small mb-0">Jaminan perlindungan kesehatan primer, rawat jalan, dan rawat inap.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- JHT -->
                            <div class="col-md-6 col-lg-4">
                                <div class="bpjs-badge-card d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <div class="bpjs-icon-box bg-primary-subtle text-primary">
                                                <i class="fas fa-piggy-bank"></i>
                                            </div>
                                            <?php if ($model->bpjs_jht): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                                    <i class="fas fa-check-circle me-1"></i> Aktif
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">
                                                    <i class="fas fa-minus-circle me-1"></i> Tidak Aktif
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">Jaminan Hari Tua (JHT)</h6>
                                        <p class="text-muted small mb-0">Tabungan hari tua akumulatif dari iuran perusahaan dan karyawan.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- JP -->
                            <div class="col-md-6 col-lg-4">
                                <div class="bpjs-badge-card d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <div class="bpjs-icon-box bg-purple-subtle text-primary" style="background-color: #f3e8ff; color: #7e22ce;">
                                                <i class="fas fa-umbrella-beach"></i>
                                            </div>
                                            <?php if ($model->bpjs_jp): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                                    <i class="fas fa-check-circle me-1"></i> Aktif
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">
                                                    <i class="fas fa-minus-circle me-1"></i> Tidak Aktif
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">Jaminan Pensiun (JP)</h6>
                                        <p class="text-muted small mb-0">Uang pensiun berkala bulanan bagi peserta saat mencapai usia pensiun.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- JKK -->
                            <div class="col-md-6 col-lg-6">
                                <div class="bpjs-badge-card d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <div class="bpjs-icon-box bg-warning-subtle text-warning-emphasis">
                                                <i class="fas fa-user-shield"></i>
                                            </div>
                                            <?php if ($model->bpjs_jkk): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                                    <i class="fas fa-check-circle me-1"></i> Aktif
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">
                                                    <i class="fas fa-minus-circle me-1"></i> Tidak Aktif
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">Jaminan Kecelakaan Kerja (JKK)</h6>
                                        <p class="text-muted small mb-0">Kompensasi dan rehabilitasi risiko kecelakaan kerja atau penyakit akibat kerja.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- JKM -->
                            <div class="col-md-6 col-lg-6">
                                <div class="bpjs-badge-card d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <div class="bpjs-icon-box bg-info-subtle text-info-emphasis">
                                                <i class="fas fa-shield-halved"></i>
                                            </div>
                                            <?php if ($model->bpjs_jkm): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                                    <i class="fas fa-check-circle me-1"></i> Aktif
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">
                                                    <i class="fas fa-minus-circle me-1"></i> Tidak Aktif
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">Jaminan Kematian (JKM)</h6>
                                        <p class="text-muted small mb-0">Santunan tunai yang diberikan kepada ahli waris peserta meninggal dunia bukan akibat kecelakaan kerja.</p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

</div>

<?php
$js = <<<JS
$(document).on('click', '.btn-copy', function(e) {
    e.preventDefault();
    var textToCopy = $(this).data('copy');
    if (!textToCopy) return;

    var btn = $(this);
    var originalHtml = btn.html();

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(textToCopy).then(function() {
            showCopyFeedback(btn, originalHtml);
        });
    } else {
        var textArea = document.createElement("textarea");
        textArea.value = textToCopy;
        textArea.style.position = "fixed";
        textArea.style.left = "-999999px";
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            document.execCommand('copy');
            showCopyFeedback(btn, originalHtml);
        } catch (err) {
            console.error('Gagal menyalin text', err);
        }
        document.body.removeChild(textArea);
    }
});

function showCopyFeedback(btn, originalHtml) {
    btn.html('<i class="fas fa-check text-success"></i>');
    if (typeof Swal !== 'undefined') {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 1800,
            timerProgressBar: true
        });
        Toast.fire({
            icon: 'success',
            title: 'Berhasil disalin ke clipboard'
        });
    }
    setTimeout(function() {
        btn.html(originalHtml);
    }, 2000);
}
JS;
$this->registerJs($js);
?>
