<?php

use app\components\ButtonActionColumn;
use app\helpers\GeneralHelper;
use app\models\master\Account;
use app\models\master\Company;
use app\models\master\Department;
use app\models\master\Position;
use app\models\trx\LeaveRequest;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var app\models\master\search\AccountSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Manajemen User';
$this->params['breadcrumbs'][] = $this->title;

// Get dynamic options
$controllerIdCompany = $this->context->id_company ?? $this->context->user->id_company;
$departments = ArrayHelper::map(Department::find()->where(['id_company' => $controllerIdCompany])->all(), 'id_department', 'name');
$positions = ArrayHelper::map(Position::find()->where(['id_company' => $controllerIdCompany])->all(), 'id_position', 'name');

$leaveTypes = \app\models\master\LeaveType::find()
    ->where(['id_company' => GeneralHelper::session('id_company')])
    ->andWhere(['is not', 'max_day', null])
    ->all();

$gridColumns = [
    ['class' => 'yii\grid\SerialColumn'],

    [
        'attribute' => 'name',
        'format' => 'raw',
        'value' => function ($model) {
            $avatar = '';
            if (!empty($model->photo)) {
                $avatar = Html::img(Yii::getAlias('@web/' . $model->photo), [
                    'class' => 'rounded-circle me-2 border',
                    'style' => 'width: 36px; height: 36px; object-fit: cover; flex-shrink: 0;',
                ]);
            } else {
                $avatar = '<div class="rounded-circle bg-light border d-flex align-items-center justify-content-center text-muted me-2" style="width: 36px; height: 36px; flex-shrink: 0;"><i class="fas fa-user small"></i></div>';
            }

            $info = Html::tag('strong', Html::encode($model->name)) . '<br>' . 
                    Html::tag('span', '<i class="fas fa-envelope text-muted"></i> ' . Html::encode($model->email), ['class' => 'small text-muted']);
            
            if (!empty($model->employee_code)) {
                $info .= ' &bull; ' . Html::tag('span', Html::encode($model->employee_code), ['class' => 'small text-muted']);
            }

            return '<div class="d-flex align-items-center">' . $avatar . '<div>' . $info . '</div></div>';
        }
    ],
    [
        'attribute' => 'employment_status',
        'filter' => Account::listEmploymentStatus(),
        'label' => 'Status Pegawai',
        'format' => 'raw',
        'value' => function ($m) {
            if (empty($m->employment_status)) {
                return '-';
            }
            $bg = match($m->employment_status) {
                'Tetap' => 'bg-success',
                'Kontrak' => 'bg-primary',
                'Probation' => 'bg-warning text-dark',
                'Magang' => 'bg-info text-dark',
                default => 'bg-secondary',
            };
            return Html::tag('span', Html::encode($m->employment_status), ['class' => 'badge ' . $bg]);
        },
        'contentOptions' => ['class' => 'text-center'],
        'headerOptions' => ['class' => 'text-center'],
    ],
    [
        'attribute' => 'id_department',
        'value' => 'department.name',
        'filter' => $departments,
        'label' => 'Departemen',
    ],
    [
        'attribute' => 'id_position',
        'value' => 'position.name',
        'filter' => $positions,
        'label' => 'Jabatan',
    ],
    [
        'attribute' => 'status',
        'format' => 'raw',
        'value' => function ($m) {
            return GeneralHelper::textLabel($m->status ? 'Active' : 'Inactive', $m->status);
        },
        'filter' => [1 => 'Active', 0 => 'Inactive'],
        'contentOptions' => ['class' => 'text-center'],
        'headerOptions' => ['class' => 'text-center'],
    ],
    [
        'label' => 'Cuti',
        'format' => 'raw',
        'value' => function ($m) use ($leaveTypes) {
            return Html::tag('span', Html::tag('strong', $m->remaining_leave . ' Hari'), ['class' => 'badge bg-info text-dark me-2']);

            $remainingLeaves = [];
            foreach ($leaveTypes as $lt) {
                $rem = LeaveRequest::getRemainingDays($m, $lt->id_leave_type);
                if ($rem !== null) {
                    $remainingLeaves[] = Html::tag('span', Html::encode($lt->name) . ': ' . Html::tag('strong', $rem . ' Hari'), ['class' => 'badge bg-info text-dark me-2']);
                }
            }
            if (!empty($remainingLeaves)) {
                return '<div class="d-flex align-items-center"><span class="me-2 text-secondary"></span> ' . implode(' ', $remainingLeaves) . '</div>';
            } else {
                return null;
            }
        }
    ],
    [
        'class' => ButtonActionColumn::className(),
        'template' => '{view} {process} {delete}',
        'urlCreator' => function ($action, Account $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->uuid]);
        }
    ],
];
?>
<?php
$company = Company::findOne($controllerIdCompany);
$currentUserCount = $company ? $company->getUserCount() : 0;
$maxUser = $company ? $company->getMaxUser() : 10;
$canAddUser = $company ? $company->canAddUser() : true;
$paket = $company ? $company->getSubscriptionPaket() : null;
$paketName = $paket ? $paket->nama_paket : ($company && $company->getActiveSubscription() ? 'Paket Kustom' : 'Standar');
$percentage = $maxUser > 0 ? min(100, round(($currentUserCount / $maxUser) * 100)) : 0;
$progressColor = $percentage >= 100 ? 'bg-danger' : ($percentage >= 80 ? 'bg-warning text-dark' : 'bg-primary');
?>

<div class="card mb-3 border-0 shadow-sm">
    <div class="card-body py-2 px-3">
        <div class="row align-items-center">
            <div class="col-md-7 col-12 d-flex flex-wrap align-items-center gap-2">
                <span class="badge bg-secondary px-2 py-1">
                    <i class="fas fa-box-open me-1"></i> Paket: <strong><?= Html::encode($paketName) ?></strong>
                </span>
                <span class="badge <?= $canAddUser ? 'bg-info text-dark' : 'bg-danger' ?> px-2 py-1">
                    <i class="fas fa-users me-1"></i> Penggunaan Kuota: <strong><?= $currentUserCount ?> / <?= $maxUser ?> User</strong>
                </span>
                <div class="progress flex-grow-1" style="height: 8px; min-width: 120px; max-width: 180px;" title="<?= $percentage ?>% terpakai">
                    <div class="progress-bar <?= $progressColor ?>" role="progressbar" style="width: <?= $percentage ?>%;" aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
            <div class="col-md-5 col-12 d-flex justify-content-start justify-content-md-end mt-2 mt-md-0">
                <?php if ($canAddUser): ?>
                    <?= Html::a(GeneralHelper::faAdd('User'), ['process'], ['class' => 'btn btn-primary']) ?>
                <?php else: ?>
                    <button type="button" class="btn btn-secondary disabled" title="Kuota user telah mencapai batas maksimal paket">
                        <i class="fas fa-ban me-1"></i> Batas Kuota Tercapai (<?= $currentUserCount ?>/<?= $maxUser ?>)
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if (!$canAddUser): ?>
    <div class="alert alert-warning d-flex align-items-center mb-3" role="alert">
        <i class="fas fa-exclamation-triangle flex-shrink-0 me-2 fs-5"></i>
        <div>
            <strong>Batas Kuota Tercapai:</strong> Perusahaan Anda telah menggunakan seluruh kuota maksimal (<strong><?= $currentUserCount ?> dari <?= $maxUser ?> user</strong>) untuk paket <strong><?= Html::encode($paketName) ?></strong>. Silakan upgrade paket langganan Anda untuk dapat menambahkan user baru.
        </div>
    </div>
<?php endif; ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'responsive' => true,
    'responsiveWrap' => false,
    'containerOptions' => ['style' => 'overflow-x: auto;'],
]);
?>
