<?php

use kartik\tabs\TabsX;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\master\Account $model */
/** @var yii\data\ActiveDataProvider $scheduleDataProvider */
/** @var yii\data\ActiveDataProvider $payrollDataProvider */

$this->title = 'Profil Saya';
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$this->registerCss("
    .profile-tabs .nav-tabs {
        flex-wrap: nowrap;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        white-space: nowrap;
        border-bottom: 2px solid #e2e8f0;
        gap: 6px;
        padding-bottom: 2px;
        margin-bottom: 1.25rem;
    }
    .profile-tabs .nav-tabs .nav-item {
        flex-shrink: 0;
    }
    .profile-tabs .nav-tabs .nav-link {
        border-radius: 8px 8px 0 0;
        padding: 0.65rem 1.25rem;
        font-weight: 500;
        color: #64748b;
        transition: all 0.2s ease;
    }
    .profile-tabs .nav-tabs .nav-link.active {
        color: #2b5fe1;
        font-weight: 600;
        border-color: #e2e8f0 #e2e8f0 #ffffff;
    }
    .profile-tabs .nav-tabs::-webkit-scrollbar {
        height: 4px;
    }
    .profile-tabs .nav-tabs::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
");

$items = [
    [
        'type' => 'profile',
        'label' => '<i class="bi bi-person-circle me-2"></i>Data Profil &amp; Akun',
        'view' => '@app/views/master/user/process',
    ],
    [
        'type' => 'schedule',
        'label' => '<i class="bi bi-calendar3-check me-2"></i>Jadwal &amp; Kehadiran',
        'view' => '@app/views/trx/attendance/index',
    ],
    [
        'type' => 'payroll',
        'label' => '<i class="bi bi-wallet2 me-2"></i>Riwayat Payroll',
        'view' => '_payroll_tab',
    ],
];

echo '<div class="profile-tabs">';
echo TabsX::widget([
    'items' => array_map(function ($num) use ($type, $data) {
        $isActive = $type == $num['type'];
        return [
            'active' => $isActive,
            'label' => $num['label'],
            'url' => Url::to(['/profile', 'type' => $num['type']]),
            'content' => $isActive ? $this->render($num['view'], $data) : '',
        ];
    }, $items),
    'position' => TabsX::POS_ABOVE,
    'encodeLabels' => false
]);
echo '</div>';
?>
