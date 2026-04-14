<?php

use app\helpers\GeneralHelper;
use kartik\date\DatePicker;
use kartik\form\ActiveForm;
use kartik\grid\GridView;
use yii\bootstrap5\Html;
use yii\data\ActiveDataProvider;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $dataProvider ActiveDataProvider */

$this->title = 'Logs';
$this->params['breadcrumbs'][] = $this->title;

$form = ActiveForm::begin(['action' => Url::toRoute('index'), 'method' => 'GET']);
?>
<div class="card mb-5">
    <div class="card-body">
        <div class="row">
            <div class="col-md-2">
                <?=
                    $form->field($filter, 'date_start')->widget(DatePicker::classname(), [
                        'options' => ['placeholder' => 'Tanggal Mulai'],
                        'removeButton' => false,
                        'pluginOptions' => [
                            'autoclose' => true,
                            'format' => 'yyyy-mm-dd'
                        ]
                    ])->label('Tanggal Mulai')
                    ?>
            </div>
            <div class="col-md-2">
                <?=
                    $form->field($filter, 'date_end')->widget(DatePicker::classname(), [
                        'options' => ['placeholder' => 'Tanggal Selesai'],
                        'removeButton' => false,
                        'pluginOptions' => [
                            'autoclose' => true,
                            'format' => 'yyyy-mm-dd'
                        ]
                    ])->label('Tanggal Selesai')
                    ?>
            </div>
            <div class="col-md-2 d-flex align-items-center">
                <?= Html::submitButton(GeneralHelper::faSearch(), ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
    </div>
</div>
<?php
$form->end();

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $model,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        // 'idlog',
        'created_at:datetime',
        [
            'attribute' => 'created_at',
            'format' => 'datetime',
            'filter' => Html::activeTextInput($model, 'created_at', ['type' => 'date', 'class' => 'form-control'])
        ],
        [
            'label' => 'Username',
            'attribute' => 'id_user',
            'value' => 'user.username',
        ],
        // 'action',
        // 'table',
        // 'id',
        'url:url',
        // 'ip',
        [
            'attribute' => 'data',
            'format' => 'raw',
            'value' => function ($m) {
                return Html::tag('div', StringHelper::truncateWords($m->data, 15), ['style' => 'max-width:500px;max-height:200px;overflow:auto;']);
            }
        ],
        // 'data:ntext',
        [
            'class' => 'app\components\ButtonActionColumn',
            'template' => '{view}'
        ],
    ],
]);
?>