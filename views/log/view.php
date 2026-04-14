<?php

use app\components\Helper;
use app\helpers\Role;
use app\models\AuthItem;
use app\models\TrsLog;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model TrsLog */

$this->title = 'Logs';
$this->params['breadcrumbs'][] = ['label' => 'Logs', 'url' => ['index']];
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="log-view">

    <?=
        DetailView::widget([
            'model' => $model,
            'attributes' => [
                [
                    'label' => 'Cabang',
                    'attribute' => 'id_branch',
                    'value' => $model->user->branch->name,
                    'visible' => Role::allBranch(),
                ],
                'user.username',
                [
                    'label' => 'Role',
                    'value' => AuthItem::getList($model->user->role->item_name),
                ],
                'created_at:datetime',
                //            'action',
                'url:url',
                // 'ip',
                [
                    'label' => 'Data',
                    'format' => 'html',
                    'value' => function ($row) {
                            return Helper::printr(json_decode($row->data));
                        }
                ],
            ],
        ])
        ?>

</div>