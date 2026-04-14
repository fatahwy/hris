<?php

use app\components\ButtonActionColumn;
use app\helpers\GeneralHelper;
use app\models\master\Shift;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\master\search\ShiftSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Shifts';
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

        'id_shift',
        'name',
        'checkin_start',
        'workhour_start',
        'workhour_end',
        [
            'label' => 'color',
            'format' => 'raw',
            'value' => function ($m) {
                return '<div style="background-color: ' . $m->color . '; width: 60px; height: 30px;"></div>';
            }
        ],
        'note:ntext',
        'created_at:datetime',
        //'updated_at',
        [
            'class' => ButtonActionColumn::className(),
            'template' => '{process} {delete}',
            'urlCreator' => function ($action, Shift $model, $key, $index, $column) {
                return Url::toRoute([$action, 'id_shift' => $model->id_shift]);
            }
        ],
    ],
]); ?>

<?php Pjax::end(); ?>