<?php

use app\models\master\Account;
use yii\bootstrap5\Html;
use yii\widgets\DetailView;
use app\helpers\GeneralHelper;

/** @var yii\web\View $this */
/** @var app\models\trx\LeaveRequest $model */

$this->title = 'Leave Request: #' . $model->id_leave_request;
$this->params['breadcrumbs'][] = ['label' => 'Leave Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="leave-request-view">

    <p class="text-end">
        <?php if ($model->isStatusPending() && Account::isUserSubmit($model)): ?>
            <?= Html::a(GeneralHelper::faUpdate(), ['process', 'id_leave_request' => $model->id_leave_request], ['class' => 'btn btn-primary']) ?>
            <?= Html::a(GeneralHelper::faDelete(), ['delete', 'id_leave_request' => $model->id_leave_request], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif; ?>
        <?php if ($model->isStatusPending() && \app\helpers\RoleHelper::approvalLeave()): ?>
            <?= Html::a('<i class="bi bi-check"></i> Approval', ['approval', 'id_leave_request' => $model->id_leave_request], [
                'class' => 'btn btn-success',
                'data-confirm' => 'Are you sure you want to process this leave request?',
            ]) ?>
        <?php endif; ?>
    </p>

    <div class="card modern-form-card shadow-sm">
        <div class="card-header bg-white border-bottom pb-2 pt-3">
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-calendar-alt text-primary me-2"></i> Detail Leave
                Request</h5>
        </div>
        <div class="card-body p-0">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'id_user',
                        'value' => function ($model) {
                            return $model->user->name ?? null;
                        }
                    ],
                    [
                        'attribute' => 'id_leave_type',
                        'value' => function ($model) {
                            return $model->leaveType->name ?? null;
                        }
                    ],
                    'start_date:date',
                    'end_date:date',
                    'total_day',
                    'reason:ntext',
                    'attachment:ntext',
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => function ($m) {
                            if ($m->isStatusApprove())
                                return GeneralHelper::textLabel($m->status, 1);
                            if ($m->isStatusReject())
                                return GeneralHelper::textLabel($m->status, 0);
                            return GeneralHelper::textLabel($m->status, 2);
                        },
                    ],
                    [
                        'attribute' => 'id_approver',
                        'value' => function ($model) {
                            return $model->approver->name ?? null;
                        },
                        'visible' => !$model->isStatusPending()
                    ],
                    [
                        'attribute' => 'approve_at',
                        'format' => 'datetime',
                        'visible' => !$model->isStatusPending()
                    ],
                    [
                        'attribute' => 'approve_reason',
                        'visible' => !$model->isStatusPending()
                    ],
                    'created_at:datetime',
                ],
            ]) ?>
        </div>
    </div>
</div>