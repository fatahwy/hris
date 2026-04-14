<?php

use app\components\ButtonActionColumn;
use app\helpers\GeneralHelper;
use app\models\master\Account;
use app\models\master\Company;
use app\models\master\Department;
use app\models\master\Position;
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

$gridColumns = [
    ['class' => 'yii\grid\SerialColumn'],

    [
        'attribute' => 'name',
        'format' => 'raw',
        'value' => function ($model) {
            return Html::tag('strong', $model->name) . '<br>' . Html::tag('span', '<i class="fas fa-envelope text-muted"></i> ' . $model->email, ['class' => 'small text-muted']);
        }
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
        'class' => ButtonActionColumn::className(),
        'template' => '{view} {process} {delete}',
        'urlCreator' => function ($action, Account $model, $key, $index, $column) {
            return Url::toRoute([$action, 'id_user' => $model->id_user]);
        }
    ],
];
?>
<p class="text-end">
    <?= Html::a(GeneralHelper::faAdd('User'), ['process'], ['class' => 'btn btn-primary']) ?>
</p>

<?php Pjax::begin(['id' => 'pjax-user-grid']); ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    // 'responsive' => true,
    // 'hover' => true,
    // 'striped' => false, // Removing striped for a cleaner minimal look
    // 'bordered' => false,
    // 'tableOptions' => [
    //     'class' => 'table table-hover table-modern mb-0',
    // ],
    // 'panel' => [
    //     'type' => '',
    //     'heading' => false, // We'll put our own nice toolbar in 'before'
    //     'before' => '<div class="d-flex justify-content-between align-items-center w-100">' .
    //         '<h5 class="mb-0 fw-bold text-dark"><i class="fas fa-users-cog me-2"></i>' . Html::encode($this->title) . '</h5>' .
    //         Html::a('<i class="fas fa-plus"></i> Tambah User', ['process'], ['class' => 'btn btn-primary']) .
    //         '</div>',
    //     'after' => false,
    //     'footer' => false,
    //     'options' => ['class' => 'card modern-grid-panel mb-4']
    // ],
    // 'toolbar' => [
    //     '{export}',
    //     '{toggleData}',
    // ],
    // 'export' => [
    //     'fontAwesome' => true,
    //     'showConfirmAlert' => false,
    //     'target' => GridView::TARGET_BLANK,
    // ],
    // 'layout' => "{items}\n<div class='card-footer bg-white d-flex justify-content-between align-items-center border-top-0'>{summary}\n{pager}</div>",
]);
?>

<?php Pjax::end(); ?>