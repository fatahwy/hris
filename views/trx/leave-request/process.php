<?php

use app\models\master\LeaveType;
use kartik\form\ActiveForm;
use kartik\select2\Select2;
use yii\bootstrap5\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

/** @var yii\web\View $this */
/** @var app\models\trx\LeaveRequest $model */

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
?>

<div class="card modern-form-card">
    <div class="card-body p-4">

        <?php $form = ActiveForm::begin(['id' => 'leave-request-form']); ?>

        <div class="mb-4 text-muted border-bottom pb-2">
            <i class="fas fa-info-circle me-1"></i> Form Leave Request
        </div>

        <?= $form->field($model, 'id_user')->textInput(['disabled' => true, 'value' => $isApproval ? $model->user->name : $this->context->user->name]) ?>

        <?= $form->field($model, 'id_leave_type')->widget(Select2::classname(), [
            'data' => $leaveTypes,
            'options' => ['placeholder' => '- Pilih Tipe Cuti -', 'disabled' => $isApproval],
        ]) ?>

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

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'start_date')->textInput(['type' => $permissionOnDuty ? 'time' : 'date', 'disabled' => $isApproval]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'end_date')->textInput(['type' => $permissionOnDuty ? 'time' : 'date', 'disabled' => $isApproval]) ?>
            </div>
        </div>

        <?= $form->field($model, 'reason')->textarea(['rows' => 3, 'disabled' => $isApproval]) ?>

        <?= $form->field($model, 'attachment')->fileInput(['disabled' => $isApproval]) ?>

        <?php if ($isApproval): ?>
            <div class="mb-4 text-muted border-bottom pb-2 mt-5">
                <i class="fas fa-check-circle me-1"></i> Approval Information
            </div>

            <?= $form->field($model, 'id_approver')->textInput(['disabled' => true, 'value' => $this->context->user->name]) ?>

            <?= $form->field($model, 'status')->dropDownList($model->optsStatus(), ['class' => 'form-select w-50']) ?>

            <?= $form->field($model, 'approve_reason')->textarea(['rows' => 3]) ?>
        <?php endif; ?>

        <div class="form-group text-end mt-5 pt-3 border-top">
            <?= Html::a('<i class="fas fa-times me-1"></i> Batal', ['index'], ['class' => 'btn btn-light px-4 me-2 border']) ?>
            <?= Html::submitButton('<i class="fas fa-save me-1"></i> Simpan', ['class' => 'btn btn-primary px-5 shadow-sm']) ?>
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