<?php

use yii\helpers\Html;
use kartik\grid\GridView;

$this->title = 'Presensi';
$this->params['breadcrumbs'][] = $this->title;

// Required logic checks based on current time
$nowStr = date('Y-m-d H:i:s');
?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'toolbar' => [
        'content' =>
            ($availableSchedule ? Html::a('<i class="bi bi-clock"></i> Clock In', ['clock', 'id' => $availableSchedule->id_schedule, 'type' => 'in'], [
                'class' => 'btn btn-primary',
                'title' => 'Clock In',
                'data-pjax' => '0'
            ]) : '') . ' ' .
            ($activeSchedule ? Html::a('<i class="bi bi-clock"></i> Clock Out', ['clock', 'id' => $activeSchedule->id_schedule, 'type' => 'out'], [
                'class' => 'btn btn-warning',
                'title' => 'Clock Out',
                'data-pjax' => '0'
            ]) : ''),
    ],
    'columns' => [
        ['class' => 'kartik\grid\SerialColumn'],

        'date:date',
        'shift_name',
        'checkin_start:datetime',
        'workhour_end:datetime',
        [
            'attribute' => 'status',
            'format' => 'raw',
            'value' => function ($model) use ($nowStr) {
                $workhourEnd = $model->date . ' ' . $model->workhour_end;
                $statusName = 'Belum Checkin';
                $badgeClass = 'bg-secondary';

                if ($model->checkin_datetime !== null && $model->checkout_datetime === null) {
                    $statusName = 'Checkin';
                    $badgeClass = 'bg-info text-dark';
                } elseif ($model->checkin_datetime === null && $nowStr > $workhourEnd) {
                    $statusName = 'Absent';
                    $badgeClass = 'bg-danger';
                } elseif ($model->checkin_datetime !== null && $model->checkout_datetime !== null) {
                    $statusName = 'Selesai';
                    $badgeClass = 'bg-success';
                }

                return Html::tag('span', Html::encode($statusName), ['class' => "badge $badgeClass"]);
            },
            'filter' => false
        ],
        'checkin_datetime:datetime',
        'checkout_datetime:datetime',
        [
            'class' => 'kartik\grid\ActionColumn',
            'template' => '{view}',
            'buttons' => [
                'view' => function ($url, $model, $key) {
                    return Html::a('<i class="bi bi-eye"></i>', ['view', 'id' => $model->id_schedule], [
                        'class' => 'btn btn-sm btn-outline-primary',
                        'title' => 'View Detail',
                        'data-pjax' => '0'
                    ]);
                },
            ],
        ],
    ],
]); ?>