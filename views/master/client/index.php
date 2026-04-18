<?php

use app\components\ButtonActionColumn;
use app\helpers\GeneralHelper;
use app\models\master\Client;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\master\search\ClientSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Client';
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

        'name',
        'expired_at:date',
        'email:email',
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
            'urlCreator' => function ($action, Client $model, $key, $index, $column) {
                return Url::toRoute([$action, 'id' => $model->uuid]);
            }
        ],
    ],
]); ?>

