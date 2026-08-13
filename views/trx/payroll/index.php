<?php

use app\helpers\GeneralHelper;
use app\helpers\RoleHelper;
use app\models\trx\Payroll;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

/** @var integer $month */
/** @var integer $year */
/** @var array $companyAllowances */
/** @var Payroll $models */

$this->title = 'Payroll';
$this->params['breadcrumbs'][] = $this->title;

$formatter = Yii::$app->formatter;
$canApprove = RoleHelper::approvalPayroll();

// Build dynamic allowance columns
$allowanceColumns = [];
if (!empty($companyAllowances)) {
    foreach ($companyAllowances as $allowance) {
        $uuid = $allowance['uuid'];
        $allowanceColumns[] = [
            'attribute' => 'allowance_' . $uuid,
            'label' => Html::encode($allowance['name']),
            'format' => 'raw',
            'hAlign' => 'right',
            'value' => function ($model) use ($uuid, $formatter) {
                $allowanceData = $model->allowance ?? [];
                if (is_string($allowanceData)) {
                    $allowanceData = json_decode($allowanceData, true) ?? [];
                }
                $allowanceIndexed = ArrayHelper::index($allowanceData, 'uuid');
                $value = $allowanceIndexed[$uuid]['value'] ?? 0;
                return $formatter->asInteger($value);
            },
        ];
    }
}

$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute' => 'user.name',
        'label' => 'Pegawai',
        'format' => 'raw',
        'value' => function ($model) {
            return Html::tag('div', Html::encode($model->user->name ?? '-'), ['class' => 'fw-bold text-uppercase']) .
                Html::tag('small', Html::encode($model->user->employee_code ?? ''), ['class' => 'text-muted']);
        },
    ],
    [
        'attribute' => 'ptkp',
        'label' => 'Status',
        'value' => function ($model) {
            return $model->ptkp ?: '-';
        },
    ],
    [
        'attribute' => 'basic_salary',
        'label' => 'Gaji Pokok',
        'format' => 'integer',
        'hAlign' => 'right',
    ],
];

// Add dynamic allowance columns
$gridColumns = array_merge($gridColumns, $allowanceColumns);

// Add remaining columns
$gridColumns[] = [
    'attribute' => 'overtime',
    'label' => 'Lembur',
    'format' => 'integer',
    'hAlign' => 'right',
];
$gridColumns[] = [
    'attribute' => 'gross_salary',
    'label' => 'Gross Salary',
    'format' => 'integer',
    'hAlign' => 'right',
    'contentOptions' => ['class' => 'money'],
];
$gridColumns[] = [
    'attribute' => 'ter',
    'label' => 'TER',
    'format' => 'raw',
    'hAlign' => 'right',
    'value' => function ($model) {
        return ($model->ter ? $model->ter * 100 : 0) . '%';
    },
];
$gridColumns[] = [
    'attribute' => 'tax',
    'label' => 'PPh',
    'format' => 'integer',
    'hAlign' => 'right',
    'contentOptions' => ['class' => 'money'],
];
$gridColumns[] = [
    'attribute' => 'dedection',
    'label' => 'Potongan',
    'format' => 'integer',
    'hAlign' => 'right',
    'contentOptions' => ['class' => 'text-danger'],
];
$gridColumns[] = [
    'attribute' => 'net_salary',
    'label' => 'Net Salary',
    'format' => 'integer',
    'hAlign' => 'right',
    'contentOptions' => ['class' => 'fw-bold'],
];
$gridColumns[] = [
    'attribute' => 'status',
    'label' => 'Status',
    'format' => 'raw',
    'hAlign' => 'center',
    'value' => function ($model) {
        if ($model->status === 'PENDING') {
            return Html::tag('span', 'PENDING', ['class' => 'badge bg-secondary']);
        } elseif ($model->status === Payroll::STATUS_DRAFT) {
            return Html::tag('span', 'DRAFT', ['class' => 'badge bg-info']);
        } else {
            return Html::tag('span', 'APPROVED', ['class' => 'badge bg-success']);
        }
    },
];
$gridColumns[] = [
    'class' => 'kartik\grid\ActionColumn',
    'template' => '{verify} {approve}',
    'buttons' => [
        'verify' => function ($url, $model, $key) {
            if ($model->status === 'PENDING') {
                return Html::a('<i class="bi bi-check2-circle"></i> V', ['verify', 'id' => $model->id_payroll], [
                    'class' => 'btn btn-outline-success btn-sm',
                    'data-title' => 'Verifikasi ke Draft',
                    'data-method' => 'post',
                    'data-confirm' => 'Apakah anda yakin akan verifikasi payroll ini?',
                ]);
            }
            return '';
        },
        'approve' => function ($url, $model, $key) use ($canApprove) {
            if ($model->status === Payroll::STATUS_DRAFT && $canApprove) {
                return Html::a('<i class="bi bi-patch-check"></i> Approve', ['approve', 'id' => $model->id_payroll], [
                    'class' => 'btn btn-outline-primary btn-sm',
                    'data-title' => 'Approve',
                    'data-method' => 'post',
                    'data-confirm' => 'Apakah anda yakin akan approve payroll ini?',
                ]);
            }
            return '';
        },
    ],
];

?>

<div class="payroll-index">
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="card-title mb-0">
                    <!-- <?= Html::encode($this->title) ?> -->
                </h4>
                <div class="d-flex gap-2">
                    <form action="<?= Url::to(['index']) ?>" method="get" class="d-flex gap-2 align-items-center">
                        <select name="month" class="form-select form-select-sm" style="width: auto;">
                            <?php foreach (GeneralHelper::getMonths() as $mCode => $mName): ?>
                                <option value="<?= $mCode ?>" <?= $month == $mCode ? 'selected' : '' ?>>
                                    <?= $mName ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <select name="year" class="form-select form-select-sm" style="width: auto;">
                            <?php for ($y = date('Y') - 2; $y <= date('Y') + 1; $y++): ?>
                                <option value="<?= $y ?>" <?= $year == $y ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                    </form>
                    <?php if (empty($models)): ?>
                        <a href="<?= Url::to(['generate', 'month' => $month, 'year' => $year]) ?>" class="btn btn-success btn-sm" data-confirm="Apakah anda yakin akan generate payroll?">
                            Generate Payroll
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <?= GridView::widget([
                'dataProvider' => new \yii\data\ArrayDataProvider([
                    'allModels' => $models,
                    'pagination' => false,
                ]),
                'toolbar' => '',
                'columns' => $gridColumns,
                'responsive' => true,
                'hover' => true,
                'bordered' => true,
                'striped' => false,
                'showPageSummary' => false,
                'emptyText' => 'Data tidak ditemukan untuk periode ini. Klik "Generate Payroll" untuk memulai.',
            ]) ?>
        </div>
    </div>
</div>