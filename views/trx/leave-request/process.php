<?php

use app\models\master\LeaveType;
use kartik\form\ActiveForm;
use kartik\select2\Select2;
use yii\bootstrap5\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

/** @var yii\web\View $this */
/** @var app\models\trx\LeaveRequest $model */

$permissionOnDuty = $permissionOnDuty ?? false;
$isApproval = !empty($isApproval);
if ($isApproval) {
    $this->title = 'Approval Izin & Cuti';
} else {
    $this->title = $model->isNewRecord ? 'Pengajuan Izin & Cuti' : 'Update Izin & Cuti';
}
$this->params['breadcrumbs'][] = ['label' => 'Izin & Cuti', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$leaveTypes = LeaveType::getList();

$initScheduleText = '';
if ($model->id_schedule && $model->schedule) {
    $s = $model->schedule;
    $workStart = date('H:i', strtotime($s->workhour_start));
    $workEnd = date('H:i', strtotime($s->workhour_end));
    $initScheduleText = $s->date . ' - ' . $s->shift_name . ' (' . $workStart . ' - ' . $workEnd . ')';
}
$enableButton = true;
?>

<div class="card modern-form-card shadow-sm border-0">
    <div class="card-header bg-white py-3 px-4 border-bottom">
        <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
            <i class="bi <?= $isApproval ? 'bi-clipboard-check' : 'bi-journal-plus' ?> text-primary"></i> <?= Html::encode($this->title) ?>
        </h5>
        <span class="text-muted small"><?= $isApproval ? 'Verifikasi dan tentukan status persetujuan pengajuan perizinan karyawan' : 'Formulir pengajuan cuti tahunan, izin sakit, maupun dinas luar kantor' ?></span>
    </div>
    <div class="card-body p-4">

        <?php $form = ActiveForm::begin([
            'id' => 'leave-request-form',
            'options' => ['enctype' => 'multipart/form-data'],
        ]); ?>

        <div class="row g-3">
            <div class="col-12 col-md-6">
                <?= $form->field($model, 'id_user')->textInput(['disabled' => true, 'value' => $isApproval ? $model->user->name : $this->context->user->name])->label('Pemohon') ?>
            </div>

            <div class="col-12 col-md-6">
                <?= $form->field($model, 'id_leave_type')->widget(Select2::classname(), [
                    'data' => $leaveTypes,
                    'options' => ['placeholder' => '- Pilih Tipe Cuti / Izin -', 'disabled' => $isApproval],
                ]) ?>
            </div>
        </div>

        <?php if ((int) $model->id_leave_type === LeaveType::L_YEARLY): ?>
            <?php
            $today = date('Y-m-d');
            $applicant = $isApproval ? $model->user : ($model->isNewRecord ? $this->context->user : $model->user);
            $entitlementDate = $applicant ? $applicant->leave_entitlement_date : null;
            $remaining = $applicant ? \app\models\trx\LeaveRequest::getRemainingDays($applicant, LeaveType::L_YEARLY, $today, $model->id_leave_request) : 0;
            $enableButton = false;
            ?>
            <?php if (!$isApproval): ?>
                <?php
                if (empty($entitlementDate)):
                    $enableButton = false;
                ?>
                    <div class="alert alert-danger d-flex align-items-center mb-3 rounded-3 shadow-sm border-0" role="alert">
                        <i class="bi bi-exclamation-octagon-fill me-2 fs-5 text-danger"></i>
                        <div class="small">
                            <strong>Perhatian:</strong> Mulai Hak Cuti Anda belum ditentukan oleh HR/Admin. Anda belum dapat mengajukan Cuti Tahunan.
                        </div>
                    </div>
                <?php
                elseif ($today < $entitlementDate):
                    $enableButton = false;
                ?>
                    <div class="alert alert-warning d-flex align-items-center mb-3 rounded-3 shadow-sm border-0" role="alert">
                        <i class="bi bi-clock-history me-2 fs-5 text-warning"></i>
                        <div class="small">
                            <strong>Perhatian:</strong> Hak cuti tahunan Anda baru mulai aktif pada tanggal <strong><?= Yii::$app->formatter->asDate($entitlementDate, 'php:d F Y') ?></strong>. Anda belum dapat mengajukan Cuti Tahunan.
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info d-flex align-items-center mb-3 rounded-3 shadow-sm border-0" role="alert">
                        <i class="bi bi-info-circle-fill me-2 fs-5 text-info"></i>
                        <div class="small">
                            Sisa kuota Cuti Tahunan yang tersedia: <strong><?= $remaining ?> Hari</strong>
                            <small class="text-muted d-block">(Mulai Hak Cuti: <?= Yii::$app->formatter->asDate($entitlementDate, 'php:d F Y') ?>)</small>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-secondary d-flex align-items-center mb-3 rounded-3 shadow-sm border-0" role="alert">
                    <i class="bi bi-person-badge me-2 fs-5 text-secondary"></i>
                    <div class="small">
                        Sisa Kuota Cuti Tahunan Pegawai: <strong><?= $applicant->remaining_leave ?? 0 ?> Hari</strong> &bull;
                        Mulai Hak Cuti: <strong><?= $entitlementDate ? Yii::$app->formatter->asDate($entitlementDate, 'php:d F Y') : 'Belum ditentukan' ?></strong>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?= $form->field($model, 'id_schedule', [
            'options' => [
                'id' => 'container-id-schedule',
                'style' => $permissionOnDuty ? '' : 'display: none;',
            ],
        ])->widget(Select2::classname(), [
            'initValueText' => $initScheduleText,
            'options' => ['placeholder' => '- Pilih Jadwal -', 'disabled' => $isApproval, 'id' => 'select-id-schedule'],
            'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => 0,
                'ajax' => [
                    'url' => Url::to(['/json/schedule/list']),
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) { return {q: params.term}; }'),
                ],
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                'templateResult' => new JsExpression('function(s) { return s.text; }'),
                'templateSelection' => new JsExpression('function(s) { return s.text; }'),
            ],
            'pluginEvents' => [
                'select2:select' => 'function(e) {
                    if (e.params && e.params.data && e.params.data.date) {
                        $("#' . Html::getInputId($model, 'start_date') . '").val(e.params.data.date);
                        $("#' . Html::getInputId($model, 'end_date') . '").val(e.params.data.date);
                    }
                }',
            ],
        ]) ?>

        <div class="row g-3">
            <div class="col-12 col-md-6">
                <?= $form->field($model, 'start_date')->textInput(['type' => $permissionOnDuty ? 'time' : 'date', 'disabled' => $isApproval])->label($permissionOnDuty ? 'Jam Mulai' : 'Tanggal Mulai') ?>
            </div>
            <div class="col-12 col-md-6">
                <?= $form->field($model, 'end_date')->textInput(['type' => $permissionOnDuty ? 'time' : 'date', 'disabled' => $isApproval])->label($permissionOnDuty ? 'Jam Selesai' : 'Tanggal Selesai') ?>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12">
                <?= $form->field($model, 'reason')->textarea(['rows' => 3, 'disabled' => $isApproval, 'placeholder' => 'Jelaskan alasan pengajuan izin atau cuti...']) ?>
            </div>

            <div class="col-12 col-md-6">
                <?= $form->field($model, 'attachment')->fileInput(['disabled' => $isApproval])->label('Lampiran Dokumen / Surat Dokter (Opsional)') ?>
            </div>
        </div>

        <?php if ($isApproval): ?>
            <div class="card bg-light border-0 rounded-3 p-3 mt-4 mb-2">
                <div class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-shield-check text-primary fs-5"></i> Keputusan Persetujuan (Approval)
                </div>

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <?= $form->field($model, 'id_approver')->textInput(['disabled' => true, 'value' => $this->context->user->name])->label('Approver / Atasan') ?>
                    </div>

                    <div class="col-12 col-md-6">
                        <?= $form->field($model, 'status')->dropDownList($model->optsStatus(), ['class' => 'form-select']) ?>
                    </div>

                    <div class="col-12">
                        <?= $form->field($model, 'approve_reason')->textarea(['rows' => 2, 'placeholder' => 'Catatan persetujuan atau alasan penolakan...']) ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="form-group d-flex flex-column-reverse flex-sm-row justify-content-end gap-2 mt-4 pt-3 border-top">
            <?= Html::a('<i class="bi bi-x-lg me-1"></i> Batal', ['index'], ['class' => 'btn btn-outline-secondary px-4 text-center']) ?>
            <?= Html::submitButton('<i class="bi bi-check-lg me-1"></i> Simpan', ['class' => 'btn btn-primary px-5 shadow-sm text-center ' . ($enableButton ? "" : "disabled")]) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>

<?php
$leaveTypeId = Html::getInputId($model, 'id_leave_type');
$scheduleInputId = Html::getInputId($model, 'id_schedule');
$pLate = LeaveType::P_LATE;
$pBackfirst = LeaveType::P_BACKFIRST;
$pLeaveoffice = LeaveType::P_LEAVEOFFICE;
$url = Url::toRoute(["process"]);

$js = <<<JS
$(document).ready(function() {
    $('#$leaveTypeId').on('change', function() {
        window.location = '$url?' + $('#leave-request-form').serialize();
    });
});
JS;
$this->registerJs($js);
?>
