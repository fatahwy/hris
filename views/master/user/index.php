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
                    'class' => 'rounded-circle me-3 border shadow-sm',
                    'style' => 'width: 40px; height: 40px; object-fit: cover; flex-shrink: 0;',
                ]);
            } else {
                $avatar = '<div class="rounded-circle bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10 d-flex align-items-center justify-content-center fw-bold me-3 shadow-sm" style="width: 40px; height: 40px; flex-shrink: 0; font-size: 0.95rem;">' . strtoupper(substr($model->name ?? 'U', 0, 1)) . '</div>';
            }

            $info = Html::tag('div', Html::encode($model->name), ['class' => 'fw-semibold text-dark']) .
                    Html::tag('div', '<i class="bi bi-envelope me-1 text-muted"></i>' . Html::encode($model->email) . 
                    (!empty($model->employee_code) ? ' &bull; <span class="badge bg-light text-secondary border font-monospace py-0.5">' . Html::encode($model->employee_code) . '</span>' : ''), ['class' => 'small text-muted d-flex align-items-center flex-wrap gap-1 mt-0.5']);

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
                'Tetap' => 'bg-success bg-opacity-10 text-success border border-success border-opacity-25',
                'Kontrak' => 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25',
                'Probation' => 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25',
                'Magang' => 'bg-info bg-opacity-10 text-info border border-info border-opacity-25',
                default => 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25',
            };
            return Html::tag('span', Html::encode($m->employment_status), ['class' => 'badge px-2.5 py-1.5 ' . $bg]);
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
        'label' => 'Sisa Cuti',
        'format' => 'raw',
        'value' => function ($m) {
            return '<span class="badge bg-light text-dark border px-2.5 py-1.5"><i class="bi bi-calendar2-check text-primary me-1"></i>' . (int)$m->remaining_leave . ' Hari</span>';
        },
        'contentOptions' => ['class' => 'text-center'],
        'headerOptions' => ['class' => 'text-center'],
    ],
    [
        'class' => ButtonActionColumn::className(),
        'template' => '{view} {process} {delete}',
        'urlCreator' => function ($action, Account $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id' => $model->uuid]);
        }
    ],
];

$company = Company::findOne($controllerIdCompany);
$currentUserCount = $company ? $company->getUserCount() : 0;
$maxUser = $company ? $company->getMaxUser() : 10;
$canAddUser = $company ? $company->canAddUser() : true;
$paket = $company ? $company->getSubscriptionPaket() : null;
$paketName = $paket ? $paket->nama_paket : ($company && $company->getActiveSubscription() ? 'Paket Kustom' : 'Standar');
$percentage = $maxUser > 0 ? min(100, round(($currentUserCount / $maxUser) * 100)) : 0;
$progressColor = $percentage >= 100 ? 'bg-danger' : ($percentage >= 80 ? 'bg-warning' : 'bg-primary');
?>

<!-- Quota Info Banner -->
<div class="card shadow-sm border-0 rounded-4 mb-3 bg-white">
    <div class="card-body p-3 p-sm-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div class="d-flex flex-wrap align-items-center gap-2 gap-sm-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                    style="width: 44px; height: 44px; background: rgba(43, 95, 225, 0.1); color: #2b5fe1;">
                    <i class="bi bi-people-fill fs-5"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="fw-bold text-dark">Kuota Akun Karyawan</span>
                        <span class="badge bg-light text-secondary border px-2 py-1 small">
                            Paket: <strong><?= Html::encode($paketName) ?></strong>
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <div class="progress" style="height: 7px; width: 140px; border-radius: 10px;">
                            <div class="progress-bar <?= $progressColor ?>" role="progressbar" style="width: <?= $percentage ?>%;" aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <span class="small text-muted"><strong><?= $currentUserCount ?></strong> dari <strong><?= $maxUser ?></strong> user terpakai (<?= $percentage ?>%)</span>
                    </div>
                </div>
            </div>
            <div class="w-100 w-md-auto d-flex justify-content-start justify-content-md-end">
                <?php if ($canAddUser): ?>
                    <?= Html::a(GeneralHelper::faAdd('Tambah Karyawan'), ['process'], ['class' => 'btn btn-primary px-3 shadow-sm w-100 w-md-auto']) ?>
                <?php else: ?>
                    <button type="button" class="btn btn-secondary disabled w-100 w-md-auto" title="Kuota user telah mencapai batas maksimal paket">
                        <i class="bi bi-slash-circle me-1"></i> Batas Kuota Tercapai
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if (!$canAddUser): ?>
    <div class="alert alert-warning d-flex align-items-center mb-3 rounded-3 shadow-sm border-0" role="alert">
        <i class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2 fs-5 text-warning"></i>
        <div class="small">
            <strong>Batas Kuota Tercapai:</strong> Perusahaan Anda telah menggunakan seluruh kuota maksimal (<strong><?= $currentUserCount ?> dari <?= $maxUser ?> user</strong>) untuk paket <strong><?= Html::encode($paketName) ?></strong>. Silakan upgrade paket langganan Anda untuk dapat menambahkan user baru.
        </div>
    </div>
<?php endif; ?>

<!-- Grid Table Card -->
<div class="card modern-grid-card shadow-sm border-0">
    <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom">
        <div>
            <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-person-lines-fill text-primary"></i> Daftar Karyawan
            </h5>
            <span class="text-muted small">Kelola data personal, status kepegawaian, jabatan, dan akun user</span>
        </div>
    </div>
    <div class="card-body p-0">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => $gridColumns,
            'responsive' => true,
            'responsiveWrap' => false,
            'tableOptions' => ['class' => 'table table-hover align-middle mb-0 text-nowrap'],
            'containerOptions' => ['style' => 'overflow-x: auto;'],
        ]); ?>
    </div>
</div>
