<?php

use app\models\AuthItem;
use app\models\master\Account;
use app\models\master\Department;
use app\models\master\Position;
use kartik\form\ActiveForm;
use kartik\select2\Select2;
use kartik\number\NumberControl;
use yii\bootstrap5\Html;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var app\models\master\Account $model */
/** @var app\models\AuthAssignment $modelAuthAssignment */
/** @var array $companyAllowances */

$currentPath = Yii::$app->request->pathInfo;
$isProfile = in_array($currentPath, ['profile', 'profile/index', 'master/user/view']);

if ($isProfile) {
    $this->title = $title ?? "Profile";
    $this->registerCss("
        .profile .form-control:disabled, .profile .form-select:disabled {
            background-color: #f8fafc !important;
            border-color: #e2e8f0 !important;
            color: #334155 !important;
        }
    ");
} else {
    $this->title = $model->isNewRecord ? 'Tambah Karyawan' : 'Ubah Karyawan';
    $this->params['breadcrumbs'][] = ['label' => 'Data User', 'url' => ['index']];
    if (!$model->isNewRecord) {
        $this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['/master/user/view', 'id' => $model->uuid]];
    }
    $this->params['breadcrumbs'][] = $this->title;
}

$controllerIdCompany = $this->context->id_company ?? $this->context->user->id_company;
$departments = ArrayHelper::map(Department::find()->where(['id_company' => $controllerIdCompany])->all(), 'id_department', 'name');
$positions = ArrayHelper::map(Position::find()->where(['id_company' => $controllerIdCompany])->all(), 'id_position', 'name');

// Get list of active users in the same company for manager selection
$managerQuery = Account::find()->where(['id_company' => $controllerIdCompany, 'status' => 1]);
if (!$model->isNewRecord && $model->id_user) {
    $managerQuery->andWhere(['!=', 'id_user', $model->id_user]);
}
$managers = ArrayHelper::map($managerQuery->all(), 'id_user', 'name');

$this->registerCss("
    .nav-tabs-custom {
        display: flex;
        gap: 8px;
        border-bottom: 2px solid #e2e8f0;
        margin-bottom: 24px;
        // overflow-x: auto;
    }
    .nav-tabs-custom .nav-link-custom {
        border: none;
        background: none;
        color: #64748b;
        padding: 12px 18px;
        font-weight: 500;
        cursor: pointer;
        border-bottom: 3px solid transparent;
        margin-bottom: -2px;
        white-space: nowrap;
        transition: all 0.2s ease-in-out;
        text-decoration: none;
    }
    .nav-tabs-custom .nav-link-custom:hover {
        color: #2563eb;
    }
    .nav-tabs-custom .nav-link-custom.active {
        color: #2563eb;
        border-bottom-color: #2563eb;
        font-weight: 600;
    }
    .section-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    .section-title {
        font-size: 15px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 10px;
    }
    .toggle-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .toggle-row:last-child {
        border-bottom: none;
    }
    .toggle-info small {
        color: #64748b;
        display: block;
        font-size: 12px;
    }
    .action-bar {
        align-items: right;
        // position: sticky;
        // bottom: 0;
        // background: #ffffff;
        // border-top: 1px solid #e2e8f0;
        // padding: 14px 20px;
        // margin: 20px -24px -24px -24px;
        // border-bottom-left-radius: 12px;
        // border-bottom-right-radius: 12px;
        // display: flex;
        // justify-content: flex-end;
        // gap: 10px;
        // z-index: 10;
        // box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .avatar-preview-box {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e2e8f0;
    }
");
?>

<div class="card modern-form-card profile">
    <div class="card-body p-4">

        <?php $form = ActiveForm::begin([
            'id' => 'form-karyawan',
            'options' => ['enctype' => 'multipart/form-data'],
        ]); ?>

        <!-- TAB NAVIGATION -->
        <div class="nav-tabs-custom" id="employeeTabs">
            <button type="button" class="nav-link-custom active" data-tab="tab-pribadi">
                <i class="fas fa-user me-2"></i> Data Pribadi
            </button>
            <button type="button" class="nav-link-custom" data-tab="tab-pekerjaan">
                <i class="fas fa-briefcase me-2"></i> Pekerjaan
            </button>
            <button type="button" class="nav-link-custom" data-tab="tab-payroll">
                <i class="fas fa-money-bill-wave me-2"></i> Payroll &amp; Pajak
            </button>
            <button type="button" class="nav-link-custom" data-tab="tab-bpjs">
                <i class="fas fa-shield-alt me-2"></i> BPJS
            </button>
        </div>

        <!-- ============================================== -->
        <!-- TAB 1: DATA PRIBADI                            -->
        <!-- ============================================== -->
        <div class="tab-pane-content" id="tab-pribadi">

            <!-- Card: Akun -->
            <div class="section-card">
                <div class="section-title">
                    <i class="fas fa-user-circle text-primary"></i> Akun
                </div>
                <div class="row g-3">
                    <div class="col-xs-12 col-md-6">
                        <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => 'Nama lengkap', 'disabled' => $isProfile]) ?>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'placeholder' => 'nama@perusahaan.com', 'type' => 'email', 'disabled' => $isProfile]) ?>
                    </div>
                    <?php if (!$isProfile): ?>
                        <div class="col-xs-12 col-md-6">
                            <?= $form->field($model, 'password')->passwordInput(['placeholder' => $model->isNewRecord ? 'Masukkan Password' : 'Kosongkan jika tidak ingin mengubah password']) ?>
                        </div>
                    <?php endif; ?>
                    <div class="col-xs-12 col-md-6">
                        <?= $form->field($model, 'phone')->textInput(['maxlength' => true, 'placeholder' => '08xxxxxxxxxx', 'disabled' => $isProfile]) ?>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <?= $isProfile ?
                            $form->field($model, 'role')->textInput(['value' => $model->role->item_name ?? '-', 'disabled' => true])
                            : $form->field($modelAuthAssignment, 'item_name')->widget(Select2::classname(), [
                                'data' => AuthItem::getList(),
                                // 'options' => ['placeholder' => '- Pilih Role -'],
                                // 'pluginOptions' => ['allowClear' => true],
                            ])
                            ?>
                    </div>
                </div>
            </div>

            <!-- Card: Identitas -->
            <div class="section-card">
                <div class="section-title">
                    <i class="fas fa-id-card text-primary"></i> Identitas
                </div>
                <div class="row g-3">
                    <div class="col-xs-12 col-md-6">
                        <?= $form->field($model, 'nik')->textInput(['maxlength' => 16, 'placeholder' => '16 digit NIK KTP', 'disabled' => $isProfile]) ?>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <?= $form->field($model, 'gender')->dropDownList(Account::listGender(), ['prompt' => '- Pilih Jenis Kelamin -', 'disabled' => $isProfile]) ?>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <?= $form->field($model, 'birth_place')->textInput(['maxlength' => true, 'placeholder' => 'Tempat lahir', 'disabled' => $isProfile]) ?>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <?= $form->field($model, 'birth_date')->textInput(['type' => 'date', 'disabled' => $isProfile]) ?>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <?= $form->field($model, 'marital_status')->dropDownList(Account::listMaritalStatus(), ['prompt' => '- Pilih Status Pernikahan -', 'disabled' => $isProfile]) ?>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <?= $form->field($model, 'religion')->dropDownList(Account::listReligion(), ['prompt' => '- Pilih Agama -', 'disabled' => $isProfile]) ?>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <?= $form->field($model, 'education')->dropDownList(Account::listEducation(), ['prompt' => '- Pilih Pendidikan Terakhir (Opsional) -', 'disabled' => $isProfile]) ?>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <div class="d-flex align-items-center gap-3">
                            <?php if ($model->photo): ?>
                                <img src="<?= Yii::getAlias('@web/' . $model->photo) ?>" alt="Foto"
                                    class="avatar-preview-box">
                            <?php endif; ?>
                            <div class="flex-grow-1">
                                <?php if (!$isProfile): ?>
                                    <?= $form->field($model, 'photo_file')->fileInput(['accept' => 'image/*'])->label('Foto Profil') ?>
                                <?php else: ?>
                                    <label class="form-label text-muted small">Foto Profil</label>
                                    <div>
                                        <?= $model->photo ? Html::a('Lihat Foto', Yii::getAlias('@web/' . $model->photo), ['target' => '_blank', 'class' => 'btn btn-sm btn-outline-primary']) : '-' ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card: Alamat -->
            <div class="section-card">
                <div class="section-title">
                    <i class="fas fa-map-marker-alt text-primary"></i> Alamat
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <?= $form->field($model, 'ktp_address')->textarea(['rows' => 2, 'placeholder' => 'Alamat lengkap sesuai KTP', 'disabled' => $isProfile]) ?>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <?= Html::activeCheckbox($model, 'is_domicile_same_ktp', [
                                'class' => 'form-check-input',
                                'id' => 'chk-domicile-same',
                                'disabled' => $isProfile,
                                'label' => 'Domisili sama dengan KTP',
                                'labelOptions' => ['class' => 'form-check-label fw-semibold'],
                            ]) ?>
                        </div>
                    </div>
                    <div class="col-12" id="box-domicile"
                        style="<?= (int) $model->is_domicile_same_ktp === 1 ? 'display: none;' : '' ?>">
                        <?= $form->field($model, 'domicile_address')->textarea(['rows' => 2, 'placeholder' => 'Alamat domisili saat ini', 'disabled' => $isProfile]) ?>
                    </div>
                </div>
            </div>

            <!-- Card: Kontak Darurat -->
            <div class="section-card">
                <div class="section-title">
                    <i class="fas fa-phone-alt text-primary"></i> Kontak Darurat
                </div>
                <div class="row g-3">
                    <div class="col-xs-12 col-md-6">
                        <?= $form->field($model, 'emergency_contact_name')->textInput(['maxlength' => true, 'placeholder' => 'Nama kontak darurat', 'disabled' => $isProfile]) ?>
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <?= $form->field($model, 'emergency_contact_relation')->textInput(['maxlength' => true, 'placeholder' => 'Suami/istri, orang tua, saudara, dll', 'disabled' => $isProfile]) ?>
                    </div>
                    <div class="col-12">
                        <?= $form->field($model, 'emergency_contact_phone')->textInput(['maxlength' => true, 'placeholder' => '08xxxxxxxxxx', 'disabled' => $isProfile]) ?>
                    </div>
                </div>
            </div>

        </div>

        <!-- ============================================== -->
        <!-- TAB 2: PEKERJAAN                               -->
        <!-- ============================================== -->
        <div class="tab-pane-content" id="tab-pekerjaan" style="display: none;">

            <!-- Card: Informasi Pekerjaan -->
            <div class="section-card">
                <div class="section-title">
                    <i class="fas fa-briefcase text-primary"></i> Informasi Pekerjaan
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <?= $form->field($model, 'employee_code')->textInput(['maxlength' => true, 'placeholder' => 'Kode / NIK Karyawan', 'disabled' => $isProfile]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'employment_status')->dropDownList(Account::listEmploymentStatus(), [
                            'id' => 'select-employment-status',
                            'prompt' => '- Pilih Status Kepegawaian -',
                            'disabled' => $isProfile,
                        ]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'id_department')->dropDownList($departments, ['prompt' => '- Pilih Departemen -', 'disabled' => $isProfile]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'id_position')->dropDownList($positions, ['prompt' => '- Pilih Jabatan -', 'disabled' => $isProfile]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'id_manager')->dropDownList($managers, ['prompt' => '- Pilih Atasan Langsung -', 'disabled' => $isProfile]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'work_location')->textInput(['placeholder' => 'Lokasi / Cabang Kerja', 'disabled' => $isProfile]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'join_date')->textInput([
                            'id' => 'input-join-date',
                            'type' => $isProfile ? 'text' : 'date',
                            'disabled' => $isProfile,
                        ]) ?>
                    </div>
                    <div class="col-md-6" id="box-contract-end"
                        style="<?= in_array($model->employment_status, ['Kontrak', 'Probation', 'Magang']) ? '' : 'display: none;' ?>">
                        <?= $form->field($model, 'contract_end_date')->textInput(['type' => 'date', 'disabled' => $isProfile]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'leave_entitlement_date')->textInput([
                            'id' => 'input-leave-entitlement',
                            'type' => 'date',
                            'disabled' => $isProfile,
                        ])->hint('<small class="text-muted">Default: sama dengan tanggal bergabung</small>') ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'status')->dropDownList([
                            1 => 'Active',
                            0 => 'Inactive',
                        ], [
                            'id' => 'select-status',
                            'disabled' => $isProfile,
                        ]) ?>
                    </div>
                </div>
            </div>

            <!-- Card: Pengunduran Diri (Muncul jika status Inactive) -->
            <div class="section-card" id="box-resign" style="<?= (int) $model->status === 0 ? '' : 'display: none;' ?>">
                <div class="section-title text-danger">
                    <i class="fas fa-user-minus"></i> Pengunduran Diri
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <?= $form->field($model, 'resign_date')->textInput(['type' => 'date', 'disabled' => $isProfile]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'resign_reason')->dropDownList(Account::listResignReason(), ['prompt' => '- Pilih Alasan Keluar -', 'disabled' => $isProfile]) ?>
                    </div>
                </div>
            </div>

            <!-- Card: Jatah Cuti -->
            <div class="section-card">
                <div class="section-title">
                    <i class="fas fa-calendar-check text-primary"></i> Jatah Cuti
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <?= $form->field($model, 'annual_leave_quota')->textInput(['type' => 'number', 'min' => 0, 'disabled' => $isProfile]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'remaining_leave')->textInput(['type' => 'number', 'min' => 0, 'disabled' => $isProfile]) ?>
                    </div>
                </div>
            </div>

        </div>

        <!-- ============================================== -->
        <!-- TAB 3: PAYROLL & PAJAK                         -->
        <!-- ============================================== -->
        <div class="tab-pane-content" id="tab-payroll" style="display: none;">

            <!-- Card: Gaji -->
            <div class="section-card">
                <div class="section-title">
                    <i class="fas fa-money-bill-wave text-primary"></i> Gaji &amp; Tunjangan
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <?= $form->field($model, 'basic_salary')->widget(NumberControl::classname(), [
                            'maskedInputOptions' => [
                                'prefix' => 'Rp ',
                                'suffix' => '',
                                'allowNegative' => false,
                                'groupSeparator' => '.',
                                'radixPoint' => ',',
                                'digits' => 0,
                                'rightAlign' => false,
                            ],
                            'displayOptions' => [
                                'placeholder' => 'Gaji Pokok',
                                'disabled' => $isProfile,
                            ],
                        ]) ?>
                    </div>
                    <?php
                    if (!empty($companyAllowances)):
                        foreach ($companyAllowances as $allowance):
                            $uuid = $allowance['uuid'];
                            echo '<div class="col-md-6">';
                            echo $form->field($model, 'allowance_items[' . $uuid . ']')->widget(NumberControl::classname(), [
                                'maskedInputOptions' => [
                                    'prefix' => 'Rp ',
                                    'suffix' => '',
                                    'allowNegative' => false,
                                    'groupSeparator' => '.',
                                    'radixPoint' => ',',
                                    'digits' => 0,
                                    'rightAlign' => false,
                                ],
                                'displayOptions' => [
                                    'placeholder' => $allowance['name'],
                                    'disabled' => $isProfile,
                                ],
                            ])->label($allowance['name'] . ($allowance['is_fixed'] == '1' ? ' <span class="badge bg-light text-primary border">Tetap</span>' : ''));
                            echo '</div>';
                        endforeach;
                    endif;
                    ?>
                </div>
            </div>

            <!-- Card: Pajak -->
            <div class="section-card">
                <div class="section-title">
                    <i class="fas fa-file-invoice-dollar text-primary"></i> Pajak
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <?= $form->field($model, 'npwp')->textInput(['maxlength' => true, 'placeholder' => '00.000.000.0-000.000', 'disabled' => $isProfile]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $isProfile ?
                            $form->field($model, 'ptkp')->textInput(['disabled' => true])
                            : $form->field($model, 'ptkp')->widget(Select2::classname(), [
                                'data' => Account::listPtkp(true),
                                'options' => ['placeholder' => '- Pilih Status PTKP -'],
                                'pluginOptions' => ['allowClear' => true],
                            ])
                            ?>
                    </div>
                    <div class="col-12">
                        <?= $form->field($model, 'tax_method')->radioList(Account::listTaxMethod(), [
                            'item' => function ($index, $label, $name, $checked, $value) use ($isProfile) {
                            $checkAttr = $checked ? 'checked' : '';
                            $disAttr = $isProfile ? 'disabled' : '';
                            return '<div class="form-check form-check-inline me-4">
                                    <input class="form-check-input" type="radio" name="' . $name . '" id="tax_method_' . $index . '" value="' . $value . '" ' . $checkAttr . ' ' . $disAttr . '>
                                    <label class="form-check-label" for="tax_method_' . $index . '">' . Html::encode($label) . '</label>
                                </div>';
                        }
                        ]) ?>
                    </div>
                </div>
            </div>

            <!-- Card: Rekening Bank -->
            <div class="section-card">
                <div class="section-title">
                    <i class="fas fa-university text-primary"></i> Rekening Bank
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <?= $form->field($model, 'bank_name')->dropDownList(Account::listBank(), ['prompt' => '- Pilih Bank -', 'disabled' => $isProfile]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'bank_account_number')->textInput(['maxlength' => true, 'placeholder' => 'Nomor Rekening', 'disabled' => $isProfile]) ?>
                    </div>
                    <div class="col-12">
                        <?= $form->field($model, 'bank_account_holder')->textInput(['maxlength' => true, 'placeholder' => 'Nama Pemilik Rekening Sesuai Buku Tabungan', 'disabled' => $isProfile]) ?>
                    </div>
                </div>
            </div>

        </div>

        <!-- ============================================== -->
        <!-- TAB 4: BPJS                                    -->
        <!-- ============================================== -->
        <div class="tab-pane-content" id="tab-bpjs" style="display: none;">

            <!-- Card: Nomor Kepesertaan -->
            <div class="section-card">
                <div class="section-title">
                    <i class="fas fa-id-badge text-primary"></i> Nomor Kepesertaan
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <?= $form->field($model, 'bpjs_kesehatan_number')->textInput(['maxlength' => true, 'placeholder' => 'No. Kartu BPJS Kesehatan', 'disabled' => $isProfile]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'bpjs_ketenagakerjaan_number')->textInput(['maxlength' => true, 'placeholder' => 'No. Kartu BPJS Ketenagakerjaan (KPJ)', 'disabled' => $isProfile]) ?>
                    </div>
                </div>
            </div>

            <!-- Card: Program yang Diikuti -->
            <div class="section-card">
                <div class="section-title">
                    <i class="fas fa-shield-alt text-primary"></i> Program BPJS yang Diikuti
                </div>

                <div class="toggle-row">
                    <div class="toggle-info">
                        <strong>BPJS Kesehatan</strong>
                        <small>Iuran ditanggung perusahaan &amp; potongan karyawan</small>
                    </div>
                    <div class="form-check form-switch fs-5">
                        <?= Html::activeCheckbox($model, 'bpjs_kesehatan', [
                            'class' => 'form-check-input',
                            'label' => false,
                            'disabled' => $isProfile,
                        ]) ?>
                    </div>
                </div>

                <div class="toggle-row">
                    <div class="toggle-info">
                        <strong>JHT (Jaminan Hari Tua)</strong>
                        <small>Tabungan hari tua BPJS Ketenagakerjaan</small>
                    </div>
                    <div class="form-check form-switch fs-5">
                        <?= Html::activeCheckbox($model, 'bpjs_jht', [
                            'class' => 'form-check-input',
                            'label' => false,
                            'disabled' => $isProfile,
                        ]) ?>
                    </div>
                </div>

                <div class="toggle-row">
                    <div class="toggle-info">
                        <strong>JP (Jaminan Pensiun)</strong>
                        <small>Pensiun bulanan saat memasuki usia pensiun</small>
                    </div>
                    <div class="form-check form-switch fs-5">
                        <?= Html::activeCheckbox($model, 'bpjs_jp', [
                            'class' => 'form-check-input',
                            'label' => false,
                            'disabled' => $isProfile,
                        ]) ?>
                    </div>
                </div>

                <div class="toggle-row">
                    <div class="toggle-info">
                        <strong>JKK (Jaminan Kecelakaan Kerja)</strong>
                        <small>Perlindungan risiko kecelakaan kerja</small>
                    </div>
                    <div class="form-check form-switch fs-5">
                        <?= Html::activeCheckbox($model, 'bpjs_jkk', [
                            'class' => 'form-check-input',
                            'label' => false,
                            'disabled' => $isProfile,
                        ]) ?>
                    </div>
                </div>

                <div class="toggle-row">
                    <div class="toggle-info">
                        <strong>JKM (Jaminan Kematian)</strong>
                        <small>Santunan kematian bukan akibat kecelakaan kerja</small>
                    </div>
                    <div class="form-check form-switch fs-5">
                        <?= Html::activeCheckbox($model, 'bpjs_jkm', [
                            'class' => 'form-check-input',
                            'label' => false,
                            'disabled' => $isProfile,
                        ]) ?>
                    </div>
                </div>

            </div>

        </div>

        <?php if (!$isProfile): ?>
            <!-- ACTION BAR -->
            <div class="action-bar">
                <?= Html::a('<i class="fas fa-times me-1"></i> Batal', ['index'], ['class' => 'btn btn-light px-4 border']) ?>
                <?= Html::submitButton('<i class="fas fa-save me-1"></i> Simpan', ['class' => 'btn btn-primary px-5 shadow-sm']) ?>
            </div>
        <?php endif; ?>

        <?php ActiveForm::end(); ?>

    </div>
</div>

<?php
$js = <<<JS
// Tab Switching logic
document.querySelectorAll('#employeeTabs .nav-link-custom').forEach(function(button) {
    button.addEventListener('click', function() {
        document.querySelectorAll('#employeeTabs .nav-link-custom').forEach(function(b) {
            b.classList.remove('active');
        });
        document.querySelectorAll('.tab-pane-content').forEach(function(pane) {
            pane.style.display = 'none';
        });

        this.classList.add('active');
        var targetId = this.getAttribute('data-tab');
        var targetPane = document.getElementById(targetId);
        if (targetPane) {
            targetPane.style.display = 'block';
        }
    });
});

// Domisili sama dengan KTP toggle
var chkDomicile = document.getElementById('chk-domicile-same');
var boxDomicile = document.getElementById('box-domicile');
if (chkDomicile && boxDomicile) {
    chkDomicile.addEventListener('change', function() {
        boxDomicile.style.display = this.checked ? 'none' : 'block';
    });
}

// Status Kepegawaian (Kontrak, Probation, Magang -> shows Tanggal Akhir Kontrak)
var selectEmpStatus = document.getElementById('select-employment-status');
var boxContractEnd = document.getElementById('box-contract-end');
function checkEmploymentStatus() {
    if (!selectEmpStatus || !boxContractEnd) return;
    var val = selectEmpStatus.value;
    if (val === 'Kontrak' || val === 'Probation' || val === 'Magang') {
        boxContractEnd.style.display = 'block';
    } else {
        boxContractEnd.style.display = 'none';
    }
}
if (selectEmpStatus) {
    selectEmpStatus.addEventListener('change', checkEmploymentStatus);
}

// Status Keaktifan (Inactive -> shows Pengunduran Diri card)
var selectStatus = document.getElementById('select-status');
var boxResign = document.getElementById('box-resign');
function checkAccountStatus() {
    if (!selectStatus || !boxResign) return;
    var val = selectStatus.value;
    if (val === '0') {
        boxResign.style.display = 'block';
    } else {
        boxResign.style.display = 'none';
    }
}
if (selectStatus) {
    selectStatus.addEventListener('change', checkAccountStatus);
}

// Tanggal bergabung default for mulai hak cuti
var inputJoinDate = document.getElementById('input-join-date');
var inputLeaveEntitlement = document.getElementById('input-leave-entitlement');
if (inputJoinDate && inputLeaveEntitlement) {
    inputJoinDate.addEventListener('change', function() {
        if (!inputLeaveEntitlement.value) {
            inputLeaveEntitlement.value = this.value;
        }
    });
}
JS;
$this->registerJs($js);
?>