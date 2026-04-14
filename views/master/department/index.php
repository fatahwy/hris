<?php

use app\components\ButtonActionColumn;
use app\helpers\GeneralHelper;
use app\helpers\RoleHelper;
use app\models\master\Client;
use app\models\master\Company;
use app\models\master\Department;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\master\search\DepartmentSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Department';
$this->params['breadcrumbs'][] = $this->title;
?>

<p class="text-end">
    <?= Html::a(GeneralHelper::faAdd($this->title), ['process'], ['class' => 'btn btn-primary']) ?>
</p>

<?php Pjax::begin(); ?>
<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

        'id_department',
        'name',
        'created_at:datetime',
        [
            'class' => ButtonActionColumn::className(),
            'template' => '{process} {delete}',
            'urlCreator' => function ($action, Department $model, $key, $index, $column) {
                return Url::toRoute([$action, 'id_department' => $model->id_department]);
            }
        ],
    ],
]); ?>

<?php Pjax::end(); ?>