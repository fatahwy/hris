<?php

use app\components\ButtonActionColumn;

use app\helpers\GeneralHelper;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\master\search\ClientSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Role';
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

        'label',
        [
            'class' => ButtonActionColumn::className(),
            'template' => '{process} {delete}',
            'urlCreator' => function ($action, $model, $key, $index, $column) {
                return Url::toRoute([$action, 'id_role' => $model->name]);
            },
            'visibleButtons' => [
                'process' => function ($model) {
                    return !in_array(strtolower($model->name), ['super', 'owner']);
                },
                'delete' => function ($model) {
                    return !in_array(strtolower($model->name), ['super', 'owner']);
                },
            ],
        ],
    ],
]); ?>

<?php Pjax::end(); ?>