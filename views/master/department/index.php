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

$this->title = 'Departemen';
$this->params['breadcrumbs'][] = $this->title;
?>

<p class="text-end">
    <?= Html::a(GeneralHelper::faAdd($this->title), ['process'], ['class' => 'btn btn-primary']) ?>
</p>

<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

        'id_department',
        'name',
        [
            'attribute' => 'created_at',
            'format' => 'datetime',
            'filterType' => GridView::FILTER_DATE,
            'filterWidgetOptions' => [
                'options' => ['prompt' => 'Pilih'],
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd'
                ],
            ],
        ],
        [
            'class' => ButtonActionColumn::className(),
            'template' => '{process} {delete}',
            'urlCreator' => function ($action, Department $model, $key, $index, $column) {
                return Url::toRoute([$action, 'id' => $model->uuid]);
            }
        ],
    ],
]); ?>

