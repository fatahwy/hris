<?php

use app\components\ButtonActionColumn;
use app\helpers\GeneralHelper;
use app\helpers\RoleHelper;
use app\models\trx\Subscription;
use kartik\grid\GridView;
use yii\bootstrap5\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\trx\search\SubscriptionSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var app\models\master\Company|null $company */
/** @var array $companyList */
/** @var array $paketList */

$this->title = 'Subscription';
$this->params['breadcrumbs'][] = $this->title;

$paket = $company ? $company->getSubscriptionPaket() : null;
$paketName = $paket ? $paket->nama_paket : 'Standar';
$isExpired = $company ? $company->isExpired() : false;
$expiredText = ($company && !empty($company->expired_at))
    ? Yii::$app->formatter->asDatetime($company->expired_at)
    : '-';
?>

<div class="subscription-index">

    <?php if ($company): ?>
        <div class="card shadow-sm border-0 rounded-4 mb-3 bg-white">
            <div class="card-body p-3 p-sm-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                            style="width: 44px; height: 44px; background: rgba(43, 95, 225, 0.1); color: #2b5fe1;">
                            <i class="bi bi-gem fs-5"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="fw-bold text-dark"><?= Html::encode($company->name) ?></span>
                                <span class="badge bg-light text-secondary border px-2 py-1 small">
                                    Paket: <strong><?= Html::encode($paketName) ?></strong>
                                </span>
                                <?php if ($isExpired): ?>
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 small">
                                        <i class="bi bi-exclamation-triangle me-1"></i>Kadaluarsa
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 small">
                                        <i class="bi bi-check-circle me-1"></i>Aktif
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="small text-muted mt-1">
                                Masa berlaku hingga: <strong class="text-dark"><?= Html::encode($expiredText) ?></strong>
                                &bull; Maksimal <strong><?= (int) $company->getMaxUser() ?></strong> User
                            </div>
                        </div>
                    </div>
                    <div class="w-100 w-md-auto d-flex justify-content-start justify-content-md-end">
                        <?= Html::a('<i class="bi bi-arrow-repeat me-1"></i> Perpanjang Subscription', ['process'], ['class' => 'btn btn-primary px-3 shadow-sm w-100 w-md-auto']) ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="card modern-grid-card shadow-sm border-0">
        <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom">
            <div>
                <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history text-primary"></i> Riwayat Subscription
                </h5>
                <span class="text-muted small">Daftar pengajuan perpanjangan paket dan status pembayaran</span>
            </div>
            <?php if (!$company): ?>
                <div>
                    <?= Html::a('<i class="bi bi-arrow-repeat me-1"></i> Perpanjang Subscription', ['process'], ['class' => 'btn btn-primary px-3 shadow-sm']) ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="card-body p-0">
            <?php Pjax::begin(['id' => 'pjax-subscription-grid']); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'responsive' => true,
                'responsiveWrap' => false,
                'tableOptions' => ['class' => 'table table-hover align-middle mb-0 text-nowrap'],
                'containerOptions' => ['style' => 'overflow-x: auto;'],
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    [
                        'attribute' => 'id_company',
                        'label' => 'Perusahaan',
                        'visible' => RoleHelper::isSuper(),
                        'filter' => $companyList,
                        'filterType' => GridView::FILTER_SELECT2,
                        'filterWidgetOptions' => [
                            'options' => ['prompt' => 'Semua Perusahaan'],
                            'pluginOptions' => ['allowClear' => true],
                        ],
                        'format' => 'raw',
                        'value' => function ($m) {
                            return '<div class="fw-semibold text-dark">' . Html::encode($m->company->name ?? '-') . '</div>';
                        },
                    ],
                    [
                        'attribute' => 'id_paket',
                        'label' => 'Paket',
                        'filter' => $paketList,
                        'format' => 'raw',
                        'value' => function ($m) {
                            return '<span class="badge bg-light text-dark border px-2.5 py-1.5"><i class="bi bi-box-seam me-1 text-primary"></i>' . Html::encode($m->paket->nama_paket ?? '-') . '</span>';
                        },
                    ],
                    [
                        'attribute' => 'jumlah_bulan',
                        'label' => 'Durasi',
                        'format' => 'raw',
                        'value' => function ($m) {
                            $months = $m->getEffectiveMonths();
                            return '<span class="badge bg-light text-secondary border font-monospace px-2.5 py-1.5">' . $months . ' Bulan (' . (int) $m->durasi . ' Hari)</span>';
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'headerOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'attribute' => 'price',
                        'label' => 'Total Harga',
                        'format' => 'raw',
                        'value' => function ($m) {
                            $price = (float) $m->price;
                            return '<span class="fw-semibold text-dark">' . ($price > 0 ? 'Rp ' . number_format($price, 0, ',', '.') : 'Gratis') . '</span>';
                        },
                    ],
                    [
                        'attribute' => 'status',
                        'filter' => Subscription::getStatusList(),
                        'format' => 'raw',
                        'value' => function ($m) {
                            return $m->getStatusLabel();
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'headerOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'attribute' => 'created_at',
                        'label' => 'Tgl Pengajuan',
                        'format' => 'datetime',
                        'contentOptions' => ['class' => 'text-muted small'],
                    ],
                    [
                        'attribute' => 'approved_at',
                        'label' => 'Tgl Diproses',
                        'format' => 'datetime',
                        'contentOptions' => ['class' => 'text-muted small'],
                    ],
                    [
                        'class' => ButtonActionColumn::class,
                        'template' => '{view} {approve} {cancel}',
                        'urlCreator' => function ($action, Subscription $model, $key, $index, $column) {
                            return Url::toRoute([$action, 'id' => $model->uuid]);
                        },
                        'buttons' => [
                            'approve' => function ($url, Subscription $model) {
                                if (!RoleHelper::isSuper() || !$model->isPending()) {
                                    return '';
                                }
                                return Html::a('<i class="bi bi-check-lg"></i>', $url, [
                                    'title' => 'Approve Subscription',
                                    'class' => 'btn btn-outline-success btn-sm',
                                    'style' => 'margin: 2px;',
                                    'data-method' => 'post',
                                    'data-confirm' => 'Apakah Anda yakin ingin menyetujui perpanjangan subscription ini?',
                                    'data-pjax' => '0',
                                    'data-bs-toggle' => 'tooltip',
                                ]);
                            },
                            'cancel' => function ($url, Subscription $model) {
                                if (!$model->isPending()) {
                                    return '';
                                }
                                return Html::a('<i class="bi bi-x-lg"></i>', $url, [
                                    'title' => 'Batalkan Pengajuan',
                                    'class' => 'btn btn-outline-danger btn-sm',
                                    'style' => 'margin: 2px;',
                                    'data-method' => 'post',
                                    'data-confirm' => 'Apakah Anda yakin ingin membatalkan pengajuan subscription ini?',
                                    'data-pjax' => '0',
                                    'data-bs-toggle' => 'tooltip',
                                ]);
                            },
                        ],
                    ],
                ],
            ]); ?>

            <?php Pjax::end(); ?>
        </div>
    </div>
</div>
