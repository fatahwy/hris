<?php

use app\components\ButtonActionColumn;
use app\helpers\GeneralHelper;
use app\models\master\LeaveType;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\master\search\LeaveTypeSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Izin & Cuti';
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

        'id_leave_type',
        'name',
        [
            'attribute' => 'category',
            'format' => 'raw',
            'value' => function ($m) {
                return LeaveType::optsCategory()[$m->category];
            },
            'filter' => LeaveType::optsCategory(),
        ],
        'max_day',
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
        //'updated_at',
        [
            'class' => ButtonActionColumn::className(),
            'template' => '{process} {delete}',
            'urlCreator' => function ($action, LeaveType $model, $key, $index, $column) {
                return Url::toRoute([$action, 'id' => $model->uuid]);
            }
        ],
    ],
]); ?>

