<?php

use app\helpers\GeneralHelper;
use app\helpers\RoleHelper;
use app\models\AuthItem;
use app\models\trx\Log;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model Log */

$this->title = 'Log Detail';
$this->params['breadcrumbs'][] = ['label' => 'Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="log-view">

    <?=
        DetailView::widget([
            'model' => $model,
            'attributes' => [
                [
                    'label' => 'Perusahaan',
                    'value' => $model->user->company->name ?? '-',
                    'visible' => RoleHelper::allCompany(),
                ],
                'user.username',
                [
                    'label' => 'Role',
                    'value' => !empty($model->user->role) ? AuthItem::getList($model->user->role->item_name) : '-',
                ],
                'created_at:datetime',
                'action',
                'url:url',
                'ip',
                [
                    'label' => 'Data',
                    'format' => 'raw',
                    'value' => function ($row) {
                        return GeneralHelper::printr(json_decode($row->data, true) ?: $row->data);
                    }
                ],
            ],
        ])
        ?>

</div>
