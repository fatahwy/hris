<?php

use app\helpers\DateHelper;
use app\models\master\Account;
use yii\bootstrap5\Html;
use yii\widgets\DetailView;
use app\helpers\GeneralHelper;

/** @var yii\web\View $this */
/** @var app\models\trx\LeaveRequest $model */

$leaveType = $model->leaveType->isCategoryLeave() ? 'Cuti' : 'Izin';
$jadwalKerja = DateHelper::getHumanRangeDate($model->start_date, $model->end_date, $permissionOnDuty);

$this->title = "Pengajuan " . $leaveType . ": " . ($model->leaveType->name ?? '') . " - " . $jadwalKerja;
$this->params['breadcrumbs'][] = ['label' => 'Izin & Cuti', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="leave-request-view">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <?= Html::a('<i class="bi bi-arrow-left me-1"></i> Kembali', ['index'], ['class' => 'btn btn-outline-secondary px-3 shadow-sm']) ?>

        <div class="d-flex flex-wrap gap-2">
            <?php if ($model->isStatusPending() && Account::isUserSubmit($model)): ?>
                <?= Html::a(GeneralHelper::faUpdate('Ubah'), ['process', 'id_leave_request' => $model->id_leave_request], ['class' => 'btn btn-primary shadow-sm']) ?>
                <?= Html::a(GeneralHelper::faDelete('Hapus'), ['delete', 'id_leave_request' => $model->id_leave_request], [
                    'class' => 'btn btn-outline-danger shadow-sm',
                    'data' => [
                        'confirm' => 'Apakah Anda yakin ingin menghapus permohonan ini?',
                        'method' => 'post',
                    ],
                ]) ?>
            <?php endif; ?>
            <?php if ($model->isStatusPending() && \app\helpers\RoleHelper::approvalLeave()): ?>
                <?= Html::a('<i class="bi bi-check-lg me-1"></i> Setujui (Approve)', ['approval', 'id_leave_request' => $model->id_leave_request], [
                    'class' => 'btn btn-success shadow-sm',
                    'data-confirm' => 'Apakah Anda yakin ingin menyetujui permohonan cuti/izin ini?',
                ]) ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="card modern-form-card shadow-sm border-0">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-calendar2-event text-primary"></i> Detail Pengajuan <?= Html::encode($leaveType) ?>
                </h5>
                <span class="text-muted small">Informasi lengkap permohonan dan status verifikasi</span>
            </div>
            <div>
                <?php
                if ($model->isStatusApprove())
                    echo GeneralHelper::textLabel($model->status, 1);
                elseif ($model->isStatusReject())
                    echo GeneralHelper::textLabel($model->status, 0);
                else
                    echo GeneralHelper::textLabel($model->status, 2);
                ?>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <?= DetailView::widget([
                    'model' => $model,
                    'options' => ['class' => 'table table-modern-detail mb-0'],
                    'attributes' => [
                        [
                            'attribute' => 'id_user',
                            'label' => 'Nama Pemohon',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return '<div class="fw-bold text-dark fs-6">' . Html::encode($model->user->name ?? '-') . '</div>' .
                                       '<span class="text-muted small">NIK: ' . Html::encode($model->user->employee_code ?? '-') . '</span>';
                            }
                        ],
                        [
                            'attribute' => 'id_leave_type',
                            'label' => 'Tipe Izin/Cuti',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return '<span class="badge bg-light text-dark border px-2.5 py-1.5"><i class="bi bi-tag text-primary me-1"></i>' . Html::encode($model->leaveType->name ?? '-') . '</span>';
                            }
                        ],
                        [
                            'attribute' => 'id_schedule',
                            'label' => 'Jadwal Terkait',
                            'value' => function ($model) {
                                if ($model->schedule) {
                                    $s = $model->schedule;
                                    $workStart = date('H:i', strtotime($s->workhour_start));
                                    $workEnd = date('H:i', strtotime($s->workhour_end));
                                    return $s->date . ' - ' . $s->shift_name . ' (' . $workStart . ' - ' . $workEnd . ')';
                                }
                                return null;
                            },
                            'visible' => !empty($model->id_schedule),
                        ],
                        [
                            'label' => ($permissionOnDuty ? 'Jam' : 'Tanggal') . " $leaveType",
                            'format' => 'raw',
                            'value' => function ($model) use ($jadwalKerja) {
                                return '<span class="fw-semibold text-dark">' . Html::encode($jadwalKerja) . '</span>';
                            },
                        ],
                        [
                            'attribute' => 'total_day',
                            'label' => 'Durasi',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return '<span class="badge bg-light text-secondary border font-monospace px-2.5 py-1.5">' . (int)$model->total_day . ' Hari</span>';
                            }
                        ],
                        'reason:ntext',
                        [
                            'attribute' => 'attachment',
                            'label' => 'Berkas Lampiran',
                            'format' => 'raw',
                            'value' => function ($model) {
                                if (!empty($model->attachment)) {
                                    return Html::a('<i class="bi bi-file-earmark-arrow-down me-1"></i> Unduh / Buka Dokumen', Yii::getAlias('@web/' . $model->attachment), ['target' => '_blank', 'class' => 'btn btn-sm btn-outline-primary px-3 shadow-sm']);
                                }
                                return '<span class="text-muted small">Tidak ada lampiran</span>';
                            }
                        ],
                        [
                            'attribute' => 'id_approver',
                            'label' => 'Disetujui / Ditolak Oleh',
                            'value' => function ($model) {
                                return $model->approver->name ?? null;
                            },
                            'visible' => !$model->isStatusPending()
                        ],
                        [
                            'attribute' => 'approve_at',
                            'label' => 'Waktu Persetujuan',
                            'format' => 'datetime',
                            'visible' => !$model->isStatusPending()
                        ],
                        [
                            'attribute' => 'approve_reason',
                            'label' => 'Catatan Approver',
                            'visible' => !$model->isStatusPending()
                        ],
                        'created_at:datetime',
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</div>
