<?php

use app\helpers\RoleHelper;
use app\models\trx\Payroll;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Manajemen Payroll';
$this->params['breadcrumbs'][] = $this->title;

$canApprove = RoleHelper::approvalPayroll();

$indonesianMonths = [
    '01' => 'Januari',
    '02' => 'Februari',
    '03' => 'Maret',
    '04' => 'April',
    '05' => 'Mei',
    '06' => 'Juni',
    '07' => 'Juli',
    '08' => 'Agustus',
    '09' => 'September',
    '10' => 'Oktober',
    '11' => 'November',
    '12' => 'Desember',
];
?>

<div class="payroll-index">
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="card-title mb-0">
                    <!-- <?= Html::encode($this->title) ?> -->
                </h4>
                <div class="d-flex gap-2">
                    <form action="<?= Url::to(['index']) ?>" method="get" class="d-flex gap-2 align-items-center">
                        <select name="month" class="form-select form-select-sm" style="width: auto;">
                            <?php foreach ($indonesianMonths as $mCode => $mName): ?>
                                <option value="<?= $mCode ?>" <?= $month == $mCode ? 'selected' : '' ?>>
                                    <?= $mName ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <select name="year" class="form-select form-select-sm" style="width: auto;">
                            <?php for ($y = date('Y') - 2; $y <= date('Y') + 1; $y++): ?>
                                <option value="<?= $y ?>" <?= $year == $y ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    </form>
                    <?php if (empty($models)): ?>
                        <button type="button" class="btn btn-success btn-sm btn-generate-trigger">
                            Generate Payroll
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle" id="payroll-table">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th>Karyawan</th>
                            <th class="text-end">Gaji Pokok</th>
                            <th class="text-end">Tunjangan</th>
                            <th class="text-end">Lembur</th>
                            <th class="text-end">Potongan</th>
                            <th class="text-end">Pajak</th>
                            <th class="text-end">Gaji Bersih</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($models)): ?>
                            <tr>
                                <td colspan="10" class="text-center py-4 text-muted">Data tidak ditemukan untuk periode ini. Klik "Generate Payroll" untuk memulai.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($models as $index => $model): ?>
                                <tr data-id="<?= $model->id_payroll ?>">
                                    <td class="text-center"><?= $index + 1 ?></td>
                                    <td>
                                        <div class="fw-bold text-uppercase"><?= Html::encode($model->user->name ?? '-') ?></div>
                                        <small class="text-muted"><?= Html::encode($model->user->employee_code ?? '') ?></small>
                                    </td>
                                    <td class="text-end p-0">
                                        <input type="text" 
                                               class="form-control form-control-sm border-0 bg-transparent text-end payroll-input money" 
                                               data-field="basic_salary" 
                                               value="<?= number_format($model->basic_salary, 0, ',', '.') ?>"
                                               <?= $model->status !== Payroll::STATUS_PENDING ? 'disabled' : '' ?>>
                                    </td>
                                    <td class="text-end p-0">
                                        <input type="text" 
                                               class="form-control form-control-sm border-0 bg-transparent text-end payroll-input money" 
                                               data-field="allowance" 
                                               value="<?= number_format($model->allowance, 0, ',', '.') ?>"
                                               <?= $model->status !== Payroll::STATUS_PENDING ? 'disabled' : '' ?>>
                                    </td>
                                    <td class="text-end p-0">
                                        <input type="text" 
                                               class="form-control form-control-sm border-0 bg-transparent text-end payroll-input money" 
                                               data-field="overtime" 
                                               value="<?= number_format($model->overtime, 0, ',', '.') ?>"
                                               <?= $model->status !== Payroll::STATUS_PENDING ? 'disabled' : '' ?>>
                                    </td>
                                    <td class="text-end p-0">
                                        <input type="text" 
                                               class="form-control form-control-sm border-0 bg-transparent text-end payroll-input money" 
                                               data-field="dedection" 
                                               value="<?= number_format($model->dedection, 0, ',', '.') ?>"
                                               <?= $model->status !== Payroll::STATUS_PENDING ? 'disabled' : '' ?>>
                                    </td>
                                    <td class="text-end p-0">
                                        <input type="text" 
                                               class="form-control form-control-sm border-0 bg-transparent text-end payroll-input money" 
                                               data-field="tax" 
                                               value="<?= number_format($model->tax, 0, ',', '.') ?>"
                                               <?= $model->status !== Payroll::STATUS_PENDING ? 'disabled' : '' ?>>
                                    </td>
                                    <td class="text-end fw-bold net-salary-cell" data-value="<?= $model->net_salary ?>">
                                        <?= number_format($model->net_salary, 0, ',', '.') ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($model->status === 'PENDING'): ?>
                                            <span class="badge bg-secondary">PENDING</span>
                                        <?php elseif ($model->status === Payroll::STATUS_DRAFT): ?>
                                            <span class="badge bg-info">DRAFT</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">APPROVED</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <?php if ($model->status === 'PENDING'): ?>
                                                <button class="btn btn-outline-success btn-verify" title="Verifikasi ke Draft">
                                                    <i class="bi bi-check2-circle"></i> V
                                                </button>
                                            <?php endif; ?>

                                            <?php if ($model->status === Payroll::STATUS_DRAFT && $canApprove): ?>
                                                <button class="btn btn-outline-primary btn-approve" title="Setujui (Approve)">
                                                    <i class="bi bi-patch-check"></i> Approve
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .payroll-input:focus {
        background-color: #fff !important;
        box-shadow: inset 0 0 0 1px #0d6efd;
        border-radius: 0;
    }
    #payroll-table td {
        padding: 0.5rem;
    }
    #payroll-table th {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }
    .is-valid {
        background-color: #d1e7dd !important;
    }
</style>

<?php
$updateUrl = Url::to(['update-cell']);
$verifyUrl = Url::to(['verify']);
$approveUrl = Url::to(['approve']);
$generateUrl = Url::to(['generate', 'month' => $month, 'year' => $year]);

$script = <<<JS
$(function() {
    function formatMoney(number) {
        return new Intl.NumberFormat('id-ID').format(number);
    }

    function unformatMoney(text) {
        return text.replace(/\./g, '').replace(/,/g, '.');
    }

    $(document).on('input', '.money', function() {
        let value = $(this).val().replace(/[^0-9]/g, '');
        if (value) {
            $(this).val(formatMoney(value));
        } else {
            $(this).val('0');
        }
    });

    $('.payroll-input').on('change', function() {
        let input = $(this);
        let row = input.closest('tr');
        let id = row.data('id');
        let field = input.data('field');
        let rawValue = unformatMoney(input.val());

        $.ajax({
            url: '{$updateUrl}',
            type: 'POST',
            data: {
                id: id,
                field: field,
                value: rawValue,
                _csrf: yii.getCsrfToken()
            },
            success: function(response) {
                if (response.success) {
                    row.find('.net-salary-cell').text(response.net_salary_formatted).data('value', response.net_salary);
                    input.addClass('is-valid');
                    setTimeout(() => input.removeClass('is-valid'), 1000);
                } else {
                    Swal.fire('Error', response.message || 'Update gagal', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Kesalahan koneksi', 'error');
            }
        });
    });

    $('.btn-generate-trigger').on('click', function() {
        Swal.fire({
            title: 'Generate Payroll?',
            text: 'Ini akan membuat data payroll untuk semua karyawan aktif pada periode ini.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Generate!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '{$generateUrl}';
            }
        });
    });

    $('.btn-verify').on('click', function() {
        let btn = $(this);
        let row = btn.closest('tr');
        let id = row.data('id');

        Swal.fire({
            title: 'Verifikasi Payroll?',
            text: 'Status akan berubah menjadi Draft dan tidak dapat diubah lagi.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Verifikasi!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{$verifyUrl}',
                    type: 'POST',
                    data: {id: id, _csrf: yii.getCsrfToken()},
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Berhasil', 'Payroll telah diverifikasi.', 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal', response.message, 'error');
                        }
                    }
                });
            }
        });
    });

    $('.btn-approve').on('click', function() {
        let btn = $(this);
        let row = btn.closest('tr');
        let id = row.data('id');

        Swal.fire({
            title: 'Setujui Payroll?',
            text: 'Anda akan menyetujui data payroll ini.',
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Ya, Setujui!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{$approveUrl}',
                    type: 'POST',
                    data: {id: id, _csrf: yii.getCsrfToken()},
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Berhasil', 'Payroll telah disetujui.', 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal', response.message, 'error');
                        }
                    }
                });
            }
        });
    });
});
JS;
$this->registerJs($script);
?>
