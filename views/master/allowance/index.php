<?php

use app\components\ButtonActionColumn;
use app\helpers\GeneralHelper;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\master\Company $company */
/** @var array $allowances */

$this->title = 'Tunjangan';
$this->params['breadcrumbs'][] = $this->title;
?>

<p class="text-end">
    <?= Html::a(GeneralHelper::faAdd($this->title), ['process'], ['class' => 'btn btn-primary']) ?>
</p>

<?= GridView::widget([
    'dataProvider' => new \yii\data\ArrayDataProvider([
        'allModels' => $allowances,
        'pagination' => false,
    ]),
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'name:text:Nama',
        [
            'label' => 'Jenis',
            'attribute' => 'is_fixed',
            'format' => 'raw',
            'value' => function ($m) {
                return $m['is_fixed'] ? '<span class="badge bg-success">Tetap</span>' : '<span class="badge bg-secondary">Tidak Tetap</span>';
            },
        ],
        [
            'class' => ButtonActionColumn::className(),
            'template' => '{process} {delete}',
            'urlCreator' => function ($action, $model, $key, $index, $column) {
                return Url::toRoute([$action, 'id' => $model['uuid']]);
            }
        ],
    ],
]); ?>
