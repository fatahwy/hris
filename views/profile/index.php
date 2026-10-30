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
        gap: 4px;
        padding-bottom: 2px;
    }
    .profile-tabs .nav-tabs .nav-item {
        flex-shrink: 0;
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
        'label' => '<i class="fas fa-user me-2"></i>Profil',
        'view' => '@app/views/master/user/process',
    ],
    [
        'type' => 'schedule',
        'label' => '<i class="fas fa-calendar-alt me-2"></i>Jadwal Kerja',
        'view' => '@app/views/trx/attendance/index',
    ],
    [
        'type' => 'payroll',
        'label' => '<i class="fas fa-money-bill-wave me-2"></i>Riwayat Payroll',
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