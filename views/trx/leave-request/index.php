<?php

use app\helpers\GeneralHelper;
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

$this->title = 'Leave Requests';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="leave-request-index">

    <p class="text-end">
        <?= Html::a(GeneralHelper::faAdd($this->title), ['process'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-leave-request-grid']); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'id_leave_request',
                'headerOptions' => ['style' => 'width: 80px;']
            ],
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
                            return $model->isStatusPending() && \app\helpers\RoleHelper::approvalLeave();
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