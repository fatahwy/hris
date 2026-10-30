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
<div class="card mb-4 shadow-sm border-0 rounded-3">
    <div class="card-body p-3 p-sm-4">
        <div class="row g-3 align-items-end">
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
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
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
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
            <div class="col-12 col-sm-12 col-md-4 col-lg-2">
                <div class="form-group mb-3">
                    <?= Html::submitButton(GeneralHelper::faSearch() . ' Cari', ['class' => 'btn btn-primary px-4 w-100 shadow-sm']) ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$form->end();

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $model,
    'responsive' => true,
    'responsiveWrap' => false,
    'containerOptions' => ['style' => 'overflow-x: auto;'],
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        // 'idlog',
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