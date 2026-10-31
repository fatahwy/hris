<?php

use app\helpers\GeneralHelper;
use app\helpers\RoleHelper;
use app\models\AuthItem;
use app\models\trx\Log;
use yii\bootstrap5\Html;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model Log */

$this->title = 'Detail Audit Log #' . $model->idlog;
$this->params['breadcrumbs'][] = ['label' => 'Log Aktivitas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="log-view">
    <div class="d-flex justify-content-end mb-3">
        <?= Html::a('<i class="bi bi-arrow-left me-1"></i> Kembali ke Log', ['index'], ['class' => 'btn btn-outline-secondary px-3 shadow-sm']) ?>
    </div>

    <div class="card modern-form-card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 px-4 border-bottom">
            <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-journal-code text-primary"></i> <?= Html::encode($this->title) ?>
            </h5>
            <span class="text-muted small">Rincian parameter request, IP address, user, dan payload data</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <?= DetailView::widget([
                    'model' => $model,
                    'options' => ['class' => 'table table-modern-detail mb-0'],
                    'attributes' => [
                        [
                            'label' => 'Perusahaan',
                            'value' => $model->user->company->name ?? '-',
                            'visible' => RoleHelper::allCompany(),
                        ],
                        [
                            'label' => 'User Pelaksana',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return '<span class="fw-semibold text-dark"><i class="bi bi-person-fill text-primary me-1"></i>' . Html::encode($model->user->username ?? '-') . '</span>';
                            }
                        ],
                        [
                            'label' => 'Role',
                            'format' => 'raw',
                            'value' => function ($model) {
                                $roleName = !empty($model->user->role) ? AuthItem::getList($model->user->role->item_name) : '-';
                                return '<span class="badge bg-light text-dark border px-2 py-1">' . Html::encode($roleName) . '</span>';
                            }
                        ],
                        'created_at:datetime',
                        [
                            'attribute' => 'action',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return '<span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1 font-monospace">' . Html::encode($model->action) . '</span>';
                            }
                        ],
                        [
                            'attribute' => 'url',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return '<code class="small text-secondary bg-light px-2 py-1 rounded">' . Html::encode($model->url) . '</code>';
                            }
                        ],
                        [
                            'attribute' => 'ip',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return '<span class="badge bg-light text-secondary border font-monospace px-2 py-1">' . Html::encode($model->ip) . '</span>';
                            }
                        ],
                        [
                            'label' => 'Payload Data JSON',
                            'format' => 'raw',
                            'value' => function ($row) {
                                $decoded = json_decode($row->data, true);
                                $content = $decoded ? json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : $row->data;
                                return '<pre class="bg-light p-3 rounded-3 border small m-0 font-monospace text-dark" style="max-height: 400px; overflow: auto; word-break: break-all;">' . Html::encode($content) . '</pre>';
                            }
                        ],
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</div>
