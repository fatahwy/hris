<?php

use yii\bootstrap5\Html;
use yii\widgets\DetailView;
use app\helpers\GeneralHelper;

/** @var yii\web\View $this */
/** @var app\models\master\Account $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Data User', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-view">

    <p class="text-end">
        <?= Html::a('<i class="fas fa-edit"></i> Update', ['process', 'id_user' => $model->id_user], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fas fa-trash"></i> Delete', ['delete', 'id_user' => $model->id_user], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <div class="card modern-form-card shadow-sm">
        <div class="card-header bg-white border-bottom pb-2 pt-3">
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-user-circle text-primary me-2"></i> Detail User</h5>
        </div>
        <div class="card-body p-0">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'name',
                    'email:email',
                    'phone',
                    'employee_code',
                    [
                        'attribute' => 'id_company',
                        'value' => function ($model) {
                                return $model->company->name ?? null;
                            }
                    ],
                    [
                        'attribute' => 'id_department',
                        'value' => function ($model) {
                                return $model->department->name ?? null;
                            }
                    ],
                    [
                        'attribute' => 'id_position',
                        'value' => function ($model) {
                                return $model->position->name ?? null;
                            }
                    ],
                    'basic_salary:currency',
                    'join_date:date',
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => function ($model) {
                                return GeneralHelper::textLabel($model->status ? 'Active' : 'Inactive', $model->status);
                            }
                    ],
                    'created_at:datetime',
                    'updated_at:datetime',
                ],
            ]) ?>
        </div>
    </div>

</div>