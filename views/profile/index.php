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
?>