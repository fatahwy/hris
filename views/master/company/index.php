<?php

use app\components\ButtonActionColumn;
use app\helpers\GeneralHelper;
use app\models\master\Company;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\master\search\CompanySearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Perusahaan';
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

        'id_company',
        'name',
        'address',
        [
            'attribute' => 'status',
            'format' => 'raw',
            'value' => function ($m) {
                return GeneralHelper::textLabel($m->status ? 'Active' : 'Inactive', $m->status);
            }
        ],
        'max_user',
        'created_at:datetime',
        //'updated_at',
        [
            'class' => ButtonActionColumn::className(),
            'template' => '{process} {delete}',
            'urlCreator' => function ($action, Company $model, $key, $index, $column) {
                return Url::toRoute([$action, 'id_company' => $model->id_company]);
            }
        ],
    ],
]); ?>

<?php Pjax::end(); ?>