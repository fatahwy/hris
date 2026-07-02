<?php

use app\helpers\GeneralHelper;
use app\helpers\RoleHelper;
use app\models\master\Account;
use app\models\trx\LeaveRequest;
use app\components\ButtonActionColumn;
use kartik\grid\GridView;
use yii\bootstrap5\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\trx\search\LeaveRequestSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Izin & Cuti';
$this->params['breadcrumbs'][] = $this->title;

$currentUser = GeneralHelper::identity();
$remainingLeavesHtml = '';
if ($currentUser) {
    $leaveTypes = \app\models\master\LeaveType::find()
        ->where(['id_company' => GeneralHelper::session('id_company')])
        ->andWhere(['is not', 'max_day', null])
        ->all();
    $remainingLeaves = [];
    foreach ($leaveTypes as $lt) {
        $rem = LeaveRequest::getRemainingDays($currentUser->id_user, $lt->id_leave_type);
        if ($rem !== null) {
            $remainingLeaves[] = Html::tag('span', Html::encode($lt->name) . ': ' . Html::tag('strong', $rem . ' Hari'), ['class' => 'badge bg-info text-dark me-2']);
        }
    }
    if (!empty($remainingLeaves)) {
        $remainingLeavesHtml = '<div class="d-flex align-items-center"><span class="me-2 text-secondary"><i class="bi bi-info-circle-fill text-info me-1"></i> <strong>Sisa Cuti Anda:</strong></span> ' . implode(' ', $remainingLeaves) . '</div>';
        // } else {
        // $remainingLeavesHtml = '<div class="text-secondary"><i class="bi bi-info-circle text-muted me-1"></i> Tidak ada kuota cuti tahunan yang didefinisikan.</div>';
    }
}
?>
<div class="leave-request-index">

    <p class="text-end">
        <?= Html::a(GeneralHelper::faAdd($this->title), ['process'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-leave-request-grid']); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'beforeHeader' => [
            [
                'columns' => [
                    [
                        'content' => $remainingLeavesHtml,
                        'options' => ['colspan' => 9, 'class' => 'bg-light p-3'],
                    ],
                ],
                'options' => ['class' => 'skip-export'],
            ],
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'id_user',
                'value' => 'user.name',
            ],
            [
                'attribute' => 'id_leave_type',
                'value' => 'leaveType.name',
            ],
            'start_date:date',
            'end_date:date',
            'total_day',
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
                'contentOptions' => ['class' => 'text-center'],
                'headerOptions' => ['class' => 'text-center'],
            ],
            [
                'class' => ButtonActionColumn::className(),
                'template' => '{view} {process} {approval} {delete}',
                'visibleButtons' => [
                    'approval' => function ($model) {
                            return $model->isStatusPending() && RoleHelper::approvalLeave();
                        },
                    'process' => function ($model) {
                            return $model->isStatusPending() && Account::isUserSubmit($model);
                        },
                    'delete' => function ($model) {
                            return $model->isStatusPending() && Account::isUserSubmit($model);
                        },
                ],
                'buttons' => [
                    'approval' => function ($url, $model, $key) {
                            return Html::a('<i class="bi bi-check"></i>', ['approval', 'id_leave_request' => $model->id_leave_request], [
                                'class' => 'btn btn-sm btn-success',
                                'title' => 'Approve',
                                'data-bs-toggle' => 'tooltip',
                                'data-confirm' => 'Are you sure you want to process this request?',
                            ]);
                        },
                ],
                'urlCreator' => function ($action, LeaveRequest $model, $key, $index, $column) {
                        return Url::toRoute([$action, 'id_leave_request' => $model->id_leave_request]);
                    }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>