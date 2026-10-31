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
    $leaveTypes = \app\models\master\LeaveType::getQueryByCompany()
        ->andWhere(['category' => \app\models\master\LeaveType::CATEGORY_LEAVE])
        ->all();
    $remainingLeaves = [];
    foreach ($leaveTypes as $lt) {
        $rem = LeaveRequest::getRemainingDays($currentUser, $lt->id_leave_type);
        if ($rem !== null) {
            $extraNote = '';
            if ((int) $lt->id_leave_type === \app\models\master\LeaveType::L_YEARLY) {
                $today = date('Y-m-d');
                if (empty($currentUser->leave_entitlement_date) || $today < $currentUser->leave_entitlement_date) {
                    $extraNote = ' <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 ms-1">Belum Aktif</span>';
                }
            }
            $remainingLeaves[] = Html::tag('span', Html::encode($lt->name) . ': ' . Html::tag('strong', $rem . ' Hari') . $extraNote, ['class' => 'badge bg-light text-dark border px-2.5 py-1.5']);
        }
    }
    if (!empty($remainingLeaves)) {
        $remainingLeavesHtml = '<div class="d-flex align-items-center flex-wrap gap-2">' . implode(' ', $remainingLeaves) . '</div>';
    } else {
        $remainingLeavesHtml = '<div class="text-muted small"><i class="bi bi-info-circle me-1"></i> Tidak ada kuota cuti tahunan yang didefinisikan.</div>';
    }
}
?>
<div class="leave-request-index">

    <!-- Leave Balance Card -->
    <?php if ($currentUser): ?>
        <div class="card shadow-sm border-0 rounded-4 mb-3 bg-white">
            <div class="card-body p-3 p-sm-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                            style="width: 44px; height: 44px; background: rgba(43, 95, 225, 0.1); color: #2b5fe1;">
                            <i class="bi bi-calendar2-heart fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark mb-1">Sisa Kuota Cuti Anda</div>
                            <?= $remainingLeavesHtml ?>
                        </div>
                    </div>
                    <div class="w-100 w-md-auto d-flex justify-content-start justify-content-md-end">
                        <?= Html::a(GeneralHelper::faAdd('Ajukan Cuti / Izin'), ['process'], ['class' => 'btn btn-primary px-3 shadow-sm w-100 w-md-auto']) ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Main Grid Card -->
    <div class="card modern-grid-card shadow-sm border-0">
        <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom">
            <div>
                <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-journal-text text-primary"></i> Riwayat Pengajuan Izin &amp; Cuti
                </h5>
                <span class="text-muted small">Daftar permohonan izin sakit, cuti tahunan, dan persetujuan atasan</span>
            </div>
        </div>
        <div class="card-body p-0">
            <?php Pjax::begin(['id' => 'pjax-leave-request-grid']); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'responsive' => true,
                'responsiveWrap' => false,
                'tableOptions' => ['class' => 'table table-hover align-middle mb-0 text-nowrap'],
                'containerOptions' => ['style' => 'overflow-x: auto;'],
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    [
                        'attribute' => 'id_user',
                        'value' => 'user.name',
                        'format' => 'raw',
                        'value' => function ($m) {
                            return '<div class="fw-semibold text-dark">' . Html::encode($m->user->name ?? '-') . '</div>';
                        }
                    ],
                    [
                        'attribute' => 'id_leave_type',
                        'value' => 'leaveType.name',
                        'format' => 'raw',
                        'value' => function ($m) {
                            return '<span class="badge bg-light text-dark border px-2.5 py-1.5"><i class="bi bi-tag me-1 text-primary"></i>' . Html::encode($m->leaveType->name ?? '-') . '</span>';
                        }
                    ],
                    'start_date:date',
                    'end_date:date',
                    [
                        'attribute' => 'total_day',
                        'format' => 'raw',
                        'value' => function ($m) {
                            return '<span class="badge bg-light text-secondary border font-monospace px-2 py-1">' . (int)$m->total_day . ' Hari</span>';
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'headerOptions' => ['class' => 'text-center'],
                    ],
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
                        'filter' => LeaveRequest::optsStatus(),
                        'filterType' => GridView::FILTER_SELECT2,
                        'filterWidgetOptions' => [
                            'options' => ['prompt' => 'Pilih'],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'width' => '100px'
                            ],
                        ],
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
                                return Html::a('<i class="bi bi-check-lg"></i>', ['approval', 'id_leave_request' => $model->id_leave_request], [
                                    'class' => 'btn btn-sm btn-outline-success',
                                    'title' => 'Setujui Pengajuan (Approve)',
                                    'data-bs-toggle' => 'tooltip',
                                    'data-confirm' => 'Apakah anda yakin akan memproses permintaan ini?',
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
    </div>
</div>
