<?php

use app\helpers\GeneralHelper;
use app\helpers\RoleHelper;
use app\models\trx\Payroll;
use kartik\grid\GridView;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;

/** @var integer $month */
/** @var integer $year */
/** @var array $companyAllowances */
/** @var Payroll $models */

$this->title = 'Payroll';
$this->params['breadcrumbs'][] = $this->title;

$formatter = Yii::$app->formatter;
$canApprove = RoleHelper::approvalPayroll();

// Build dynamic allowance columns
$allowanceColumns = [];
if (!empty($companyAllowances)) {
    foreach ($companyAllowances as $allowance) {
        $uuid = $allowance['uuid'];
        $allowanceColumns[] = [
            'attribute' => 'allowance_' . $uuid,
            'label' => Html::encode($allowance['name']),
            'format' => 'raw',
            'hAlign' => 'right',
            'value' => function ($model) use ($uuid, $formatter) {
                $allowanceData = $model->allowance ?? [];
                if (is_string($allowanceData)) {
                    $allowanceData = json_decode($allowanceData, true) ?? [];
                }
                $allowanceIndexed = ArrayHelper::index($allowanceData, 'uuid');
                $value = $allowanceIndexed[$uuid]['value'] ?? 0;
                return $formatter->asInteger($value);
            },
        ];
    }
}

$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute' => 'user.name',
        'label' => 'Pegawai',
        'format' => 'raw',
        'value' => function ($model) {
            return Html::tag('div', Html::encode($model->user->name ?? '-'), ['class' => 'fw-bold text-uppercase']) .
                Html::tag('small', Html::encode($model->user->employee_code ?? ''), ['class' => 'text-muted']);
        },
    ],
    [
        'attribute' => 'ptkp',
        'label' => 'Status',
        'value' => function ($model) {
            return $model->ptkp ?: '-';
        },
    ],
    [
        'attribute' => 'basic_salary',
        'label' => 'Gaji Pokok',
        'format' => 'integer',
        'hAlign' => 'right',
    ],
];

// Add dynamic allowance columns
$gridColumns = array_merge($gridColumns, $allowanceColumns);

// Add remaining columns
$gridColumns[] = [
    'attribute' => 'overtime',
    'label' => 'Lembur',
    'format' => 'integer',
    'hAlign' => 'right',
];
$gridColumns[] = [
    'attribute' => 'gross_salary',
    'label' => 'Gross Salary',
    'format' => 'integer',
    'hAlign' => 'right',
    'contentOptions' => ['class' => 'money'],
];
$gridColumns[] = [
    'attribute' => 'ter',
    'label' => 'TER',
    'format' => 'raw',
    'hAlign' => 'right',
    'value' => function ($model) {
        return ($model->ter ? $model->ter * 100 : 0) . '%';
    },
];
$gridColumns[] = [
    'attribute' => 'tax',
    'label' => 'PPh',
    'format' => 'integer',
    'hAlign' => 'right',
    'contentOptions' => ['class' => 'money'],
];
$gridColumns[] = [
    'attribute' => 'dedection',
    'label' => 'Potongan',
    'format' => 'raw',
    'hAlign' => 'right',
    'contentOptions' => ['class' => 'text-danger'],
    'value' => function ($model) use ($formatter, $month, $year) {
        $amount = (float) $model->dedection;
        $desc = $model->dedection_description;
        $isPending = ($model->status === Payroll::STATUS_PENDING);

        $html = '<div class="d-flex align-items-center justify-content-end gap-1">';
        $html .= '<span class="fw-semibold">' . ($amount > 0 ? $formatter->asInteger($amount) : '0') . '</span>';

        if ($isPending) {
            $html .= Html::button('<i class="bi bi-pencil-square"></i>', [
                'class' => 'btn btn-link text-danger p-0 ms-1 btn-edit-deduction',
                'style' => 'text-decoration: none; font-size: 0.95rem; line-height: 1;',
                'title' => 'Input / Ubah Potongan',
                'data-id' => $model->id_payroll,
                'data-name' => $model->user ? $model->user->name : '',
                'data-dedection' => $model->dedection ?: 0,
                'data-description' => $model->dedection_description ?: '',
                'data-month' => $month,
                'data-year' => $year,
                'data-bs-toggle' => 'modal',
                'data-bs-target' => '#modalDeduction',
            ]);
        }
        $html .= '</div>';

        if (!empty($desc)) {
            $html .= '<div class="small text-muted text-end fst-italic" style="font-size: 0.75rem;">' . Html::encode($desc) . '</div>';
        }

        return $html;
    },
];
$gridColumns[] = [
    'label' => 'BPJS Kes (1%)',
    'format' => 'raw',
    'hAlign' => 'right',
    'contentOptions' => ['class' => 'text-danger'],
    'value' => function ($model) use ($formatter) {
        $amount = $model->getBpjsKesehatanAmount();
        return $amount > 0 ? $formatter->asInteger($amount) : '-';
    },
];
$gridColumns[] = [
    'label' => 'BPJS JHT (2%)',
    'format' => 'raw',
    'hAlign' => 'right',
    'contentOptions' => ['class' => 'text-danger'],
    'value' => function ($model) use ($formatter) {
        $amount = $model->getBpjsJhtAmount();
        return $amount > 0 ? $formatter->asInteger($amount) : '-';
    },
];
$gridColumns[] = [
    'label' => 'BPJS JP (1%)',
    'format' => 'raw',
    'hAlign' => 'right',
    'contentOptions' => ['class' => 'text-danger'],
    'value' => function ($model) use ($formatter) {
        $amount = $model->getBpjsJpAmount();
        return $amount > 0 ? $formatter->asInteger($amount) : '-';
    },
];
$gridColumns[] = [
    'attribute' => 'net_salary',
    'label' => 'Net Salary',
    'format' => 'integer',
    'hAlign' => 'right',
    'contentOptions' => ['class' => 'fw-bold'],
];
$gridColumns[] = [
    'attribute' => 'status',
    'label' => 'Status',
    'format' => 'raw',
    'hAlign' => 'center',
    'value' => function ($model) {
        if ($model->status === 'PENDING') {
            return Html::tag('span', 'PENDING', ['class' => 'badge bg-secondary']);
        } elseif ($model->status === Payroll::STATUS_DRAFT) {
            return Html::tag('span', 'DRAFT', ['class' => 'badge bg-info']);
        } else {
            return Html::tag('span', 'APPROVED', ['class' => 'badge bg-success']);
        }
    },
];
$gridColumns[] = [
    'class' => 'kartik\grid\ActionColumn',
    'template' => '{verify} {approve} {cancel-verify} {slip} {cancel-approve}',
    'buttons' => [
        'verify' => function ($url, $model, $key) {
            if ($model->status === Payroll::STATUS_PENDING) {
                return Html::a('<i class="bi bi-check2-circle"></i> V', ['verify', 'id' => $model->id_payroll], [
                    'class' => 'btn btn-outline-success btn-sm',
                    'title' => 'Verifikasi ke Draft',
                    'data-title' => 'Verifikasi ke Draft',
                    'data-method' => 'post',
                    'data-confirm' => 'Apakah anda yakin akan verifikasi payroll ini?',
                ]);
            }
            return '';
        },
        'approve' => function ($url, $model, $key) use ($canApprove) {
            if ($model->status === Payroll::STATUS_DRAFT && $canApprove) {
                return Html::a('<i class="bi bi-patch-check"></i> Approve', ['approve', 'id' => $model->id_payroll], [
                    'class' => 'btn btn-outline-primary btn-sm',
                    'title' => 'Approve',
                    'data-title' => 'Approve',
                    'data-method' => 'post',
                    'data-confirm' => 'Apakah anda yakin akan approve payroll ini?',
                ]);
            }
            return '';
        },
        'cancel-verify' => function ($url, $model, $key) {
            if ($model->status === Payroll::STATUS_DRAFT) {
                return Html::a('<i class="bi bi-arrow-counterclockwise"></i> Batal Draft', ['cancel-verify', 'id' => $model->id_payroll], [
                    'class' => 'btn btn-outline-warning btn-sm',
                    'title' => 'Batal Draft ke Pending',
                    'data-title' => 'Batal Draft ke Pending',
                    'data-method' => 'post',
                    'data-confirm' => 'Apakah anda yakin akan membatalkan status draft dan mengembalikan ke pending?',
                ]);
            }
            return '';
        },
        'slip' => function ($url, $model, $key) {
            if ($model->status === Payroll::STATUS_APPROVE) {
                return Html::a('<i class="bi bi-file-earmark-pdf"></i> Slip PDF', ['slip', 'id' => $model->id_payroll], [
                    'class' => 'btn btn-outline-danger btn-sm',
                    'target' => '_blank',
                    'data-pjax' => '0',
                    'title' => 'Cetak / Unduh Slip Gaji PDF',
                ]);
            }
            return '';
        },
        'cancel-approve' => function ($url, $model, $key) use ($canApprove) {
            if ($model->status === Payroll::STATUS_APPROVE && $canApprove) {
                return Html::a('<i class="bi bi-arrow-counterclockwise"></i> Batal Approve', ['cancel-approve', 'id' => $model->id_payroll], [
                    'class' => 'btn btn-outline-warning btn-sm',
                    'title' => 'Batal Approve ke Draft',
                    'data-title' => 'Batal Approve ke Draft',
                    'data-method' => 'post',
                    'data-confirm' => 'Apakah anda yakin akan membatalkan persetujuan payroll ini dan mengembalikan ke draft?',
                ]);
            }
            return '';
        },
    ],
];

?>

<div class="payroll-index">
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body">
            <?php Pjax::begin(['id' => 'pjax-payroll-grid', 'enablePushState' => true, 'timeout' => 10000]); ?>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="card-title mb-0">
                    <!-- <?= Html::encode($this->title) ?> -->
                </h4>
                <div class="d-flex gap-2">
                    <form id="form-payroll-filter" action="<?= Url::to(['index']) ?>" method="get" class="d-flex gap-2 align-items-center">
                        <div style="min-width: 150px;">
                            <?= Select2::widget([
                                'id' => 'payroll-month',
                                'name' => 'month',
                                'value' => (string)$month,
                                'data' => GeneralHelper::getMonths(),
                                'theme' => Select2::THEME_BOOTSTRAP,
                                'size' => Select2::SMALL,
                                'options' => [
                                    'placeholder' => 'Pilih Bulan...',
                                ],
                                'pluginOptions' => [
                                    'allowClear' => false,
                                    'width' => '100%',
                                ],
                            ]) ?>
                        </div>
                        <div style="min-width: 110px;">
                            <?php
                            $years = [];
                            for ($y = date('Y') - 2; $y <= date('Y') + 1; $y++) {
                                $years[$y] = (string)$y;
                            }
                            ?>
                            <?= Select2::widget([
                                'id' => 'payroll-year',
                                'name' => 'year',
                                'value' => (string)$year,
                                'data' => $years,
                                'theme' => Select2::THEME_BOOTSTRAP,
                                'size' => Select2::SMALL,
                                'options' => [
                                    'placeholder' => 'Pilih Tahun...',
                                ],
                                'pluginOptions' => [
                                    'allowClear' => false,
                                    'width' => '100%',
                                ],
                            ]) ?>
                        </div>
                        <div id="filter-spinner" class="spinner-border spinner-border-sm text-primary d-none" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </form>
                    <?php if (empty($models)): ?>
                        <a href="<?= Url::to(['generate', 'month' => $month, 'year' => $year]) ?>"
                            class="btn btn-success btn-sm" data-confirm="Apakah anda yakin akan generate payroll?" data-pjax="0">
                            Generate Payroll
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="position-relative">
                <div id="payroll-grid-loader" class="position-absolute top-0 start-0 w-100 h-100 d-none flex-column align-items-center justify-content-center bg-white bg-opacity-75" style="z-index: 1050; min-height: 250px; backdrop-filter: blur(2px);">
                    <div class="text-center p-3 rounded shadow-sm bg-white border">
                        <div class="spinner-border text-primary mb-2" role="status" style="width: 2.2rem; height: 2.2rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div class="text-secondary fw-semibold small">Memuat data payroll...</div>
                    </div>
                </div>

                <?= GridView::widget([
                    'dataProvider' => new \yii\data\ArrayDataProvider([
                        'allModels' => $models,
                        'pagination' => false,
                    ]),
                    'toolbar' => '',
                    'columns' => $gridColumns,
                    'responsive' => true,
                    'hover' => true,
                    'bordered' => true,
                    'striped' => false,
                    'showPageSummary' => false,
                    'emptyText' => 'Data tidak ditemukan untuk periode ini. Klik "Generate Payroll" untuk memulai.',
                ]) ?>
            </div>
            <?php Pjax::end(); ?>
        </div>
    </div>
</div>

<!-- Modal Edit Potongan Gaji -->
<div class="modal fade" id="modalDeduction" tabindex="-1" aria-labelledby="modalDeductionLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow border-0">
            <form id="form-deduction" method="post" action="">
                <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>"
                    value="<?= Yii::$app->request->csrfToken ?>">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold" id="modalDeductionLabel">
                        <i class="bi bi-pencil-square text-primary me-2"></i>Input Potongan Gaji
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info py-2 px-3 small mb-3">
                        <i class="bi bi-info-circle me-1"></i> Potongan gaji dapat diinput manual selama status payroll
                        masih <strong>PENDING</strong>. Gaji bersih akan dihitung ulang secara otomatis.
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-semibold">Nama Pegawai</label>
                        <input type="text" id="deduction-employee-name" class="form-control bg-light" readonly disabled>
                    </div>
                    <div class="mb-3">
                        <label for="deduction-amount" class="form-label fw-semibold">Nilai Potongan (Rp) <span
                                class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" id="deduction-amount" name="dedection" class="form-control" min="0"
                                step="1" required placeholder="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="deduction-desc" class="form-label fw-semibold">Keterangan / Deskripsi
                            Potongan</label>
                        <textarea id="deduction-desc" name="dedection_description" class="form-control" rows="3"
                            placeholder="Contoh: Potongan Keterlambatan, Kasbon, Pinjaman Karyawan, Denda..."></textarea>
                        <div class="form-text small text-muted">Deskripsi ini akan dicantumkan pada tabel payroll dan
                            slip gaji pegawai.</div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-check-lg me-1"></i> Simpan Potongan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$indexUrl = Url::to(['index']);
$updateDeductionBaseUrl = Url::to(['update-deduction']);
$js = <<<JS
function showPayrollLoading() {
    $('#payroll-grid-loader').removeClass('d-none').addClass('d-flex');
    $('#filter-spinner').removeClass('d-none');
    $('#payroll-month, #payroll-year').prop('disabled', true);
}

function hidePayrollLoading() {
    $('#payroll-grid-loader').removeClass('d-flex').addClass('d-none');
    $('#filter-spinner').addClass('d-none');
    $('#payroll-month, #payroll-year').prop('disabled', false);
}

$(document).on('pjax:send', '#pjax-payroll-grid', function() {
    showPayrollLoading();
});

$(document).on('pjax:complete pjax:error', '#pjax-payroll-grid', function() {
    hidePayrollLoading();
});

$(document).on('change', '#payroll-month, #payroll-year', function() {
    var month = $('#payroll-month').val();
    var year = $('#payroll-year').val();
    var baseUrl = '{$indexUrl}';
    var separator = baseUrl.indexOf('?') >= 0 ? '&' : '?';
    var targetUrl = baseUrl + separator + 'month=' + encodeURIComponent(month) + '&year=' + encodeURIComponent(year);

    showPayrollLoading();

    $.pjax({
        url: targetUrl,
        container: '#pjax-payroll-grid',
        push: true,
        replace: false,
        timeout: 10000
    });
});

$(document).on('submit', '#form-payroll-filter', function(e) {
    e.preventDefault();
    $('#payroll-month').trigger('change');
});

var modalDeduction = document.getElementById('modalDeduction');
if (modalDeduction) {
    modalDeduction.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        if (button) {
            var id = button.getAttribute('data-id') || '';
            var name = button.getAttribute('data-name') || '';
            var dedection = button.getAttribute('data-dedection') || '0';
            var desc = button.getAttribute('data-description') || '';
            var currentMonth = $('#payroll-month').val() || '{$month}';
            var currentYear = $('#payroll-year').val() || '{$year}';
            var month = button.getAttribute('data-month') || currentMonth;
            var year = button.getAttribute('data-year') || currentYear;

            var actionUrl = '{$updateDeductionBaseUrl}?id=' + id + '&month=' + encodeURIComponent(month) + '&year=' + encodeURIComponent(year);
            var form = document.getElementById('form-deduction');
            if (form) {
                form.setAttribute('action', actionUrl);
            }
            document.getElementById('deduction-employee-name').value = name;
            document.getElementById('deduction-amount').value = dedection;
            document.getElementById('deduction-desc').value = desc;
        }
    });
}

// Handle Form Submission via AJAX & reload GridView with PJAX
$(document).on('submit', '#form-deduction', function(e) {
    e.preventDefault();
    var form = $(this);
    var submitBtn = form.find('button[type="submit"]');
    var originalBtnHtml = submitBtn.html();

    submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menyimpan...');

    $.ajax({
        url: form.attr('action'),
        type: 'POST',
        data: form.serialize(),
        dataType: 'json',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(response) {
            submitBtn.prop('disabled', false).html(originalBtnHtml);
            if (response && response.success) {
                var modalEl = document.getElementById('modalDeduction');
                var modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) {
                    modalInstance.hide();
                } else {
                    $('#modalDeduction').modal('hide');
                }

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message || 'Potongan gaji berhasil diperbarui.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }

                // Reload GridView via PJAX
                var currentMonth = $('#payroll-month').val() || '{$month}';
                var currentYear = $('#payroll-year').val() || '{$year}';
                var baseUrl = '{$indexUrl}';
                var separator = baseUrl.indexOf('?') >= 0 ? '&' : '?';
                var reloadUrl = baseUrl + separator + 'month=' + encodeURIComponent(currentMonth) + '&year=' + encodeURIComponent(currentYear);

                $.pjax.reload({
                    container: '#pjax-payroll-grid',
                    url: reloadUrl,
                    async: false,
                    timeout: 10000
                });
            } else {
                var errorMsg = (response && response.message) ? response.message : 'Terjadi kesalahan saat menyimpan potongan.';
                if (typeof Swal !== 'undefined') {
                    Swal.fire('Gagal', errorMsg, 'error');
                } else {
                    alert(errorMsg);
                }
            }
        },
        error: function(xhr) {
            submitBtn.prop('disabled', false).html(originalBtnHtml);
            var errorMsg = 'Terjadi kesalahan pada server saat menyimpan potongan.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            if (typeof Swal !== 'undefined') {
                Swal.fire('Gagal', errorMsg, 'error');
            } else {
                alert(errorMsg);
            }
        }
    });
});
JS;
$this->registerJs($js);
?>