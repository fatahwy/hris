<?php

use app\models\master\LeaveType;
use app\models\master\Account;
use kartik\form\ActiveForm;
use kartik\select2\Select2;
use yii\bootstrap5\Html;
use yii\helpers\ArrayHelper;
use app\helpers\GeneralHelper;

/** @var yii\web\View $this */
/** @var app\models\trx\LeaveRequest $model */

$isApproval = !empty($isApproval);
if ($isApproval) {
    $this->title = 'Approval Leave Request';
} else {
    $this->title = $model->isNewRecord ? 'Create Leave Request' : 'Update Leave Request';
}
$this->params['breadcrumbs'][] = ['label' => 'Leave Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$leaveTypes = ArrayHelper::map(LeaveType::find()->all(), 'id_leave_type', 'name');
$users = ArrayHelper::map(Account::find()->all(), 'id_user', 'name');
?>

<div class="card modern-form-card">
    <div class="card-body p-4">

        <?php $form = ActiveForm::begin(); ?>

        <div class="mb-4 text-muted border-bottom pb-2">
            <i class="fas fa-info-circle me-1"></i> Form Leave Request
        </div>

        <?= $form->field($model, 'id_user')->textInput(['disabled' => true, 'value' => $isApproval ? $model->user->name : $this->context->user->name]) ?>

        <?= $form->field($model, 'id_leave_type')->widget(Select2::classname(), [
            'data' => $leaveTypes,
            'options' => ['placeholder' => '- Pilih Tipe Cuti -', 'disabled' => $isApproval],
        ]) ?>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'start_date')->textInput(['type' => 'date', 'disabled' => $isApproval]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'end_date')->textInput(['type' => 'date', 'disabled' => $isApproval]) ?>
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