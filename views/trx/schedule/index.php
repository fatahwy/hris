<?php

use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\master\Account[] $users */
/** @var app\models\master\Shift[] $shifts */

$this->title = 'Jadwal Kerja';
// $this->params['breadcrumbs'][] = ['label' => 'Jadwal Kerja', 'url' => ['/trx/schedule']];
$this->params['breadcrumbs'][] = $this->title;

// Register FullCalendar 6 via CDN (Styles are injected by JS)
$this->registerJsFile('https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js', ['position' => \yii\web\View::POS_END, 'depends' => [\yii\web\JqueryAsset::class]]);

$userList = ArrayHelper::map($users, 'id_user', 'name');
$shiftList = [];
foreach ($shifts as $shift) {
    $shiftList[$shift->id_shift] = "{$shift->name} ({$shift->workhour_start}-{$shift->workhour_end})";
}

?>

<div class="schedule-index">

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label fw-bold">User</label>
                    <?= Select2::widget([
                        'name' => 'user_id',
                        'id' => 'select-user',
                        'data' => $userList,
                        'options' => ['placeholder' => 'Select User...'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]) ?>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Shift</label>
                    <?= Select2::widget([
                        'name' => 'shift_id',
                        'id' => 'select-shift',
                        'data' => $shiftList,
                        'options' => ['placeholder' => 'Select Shift...'],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div id="calendar" style="min-height: 600px;"></div>
        </div>
    </div>

</div>

<style>
    /* Match the design in the image */
    .fc-header-toolbar {
        margin-bottom: 2rem !important;
    }

    .fc-toolbar-title {
        font-size: 1.5rem !important;
        font-weight: 500 !important;
        color: #444;
    }

    .fc-button {
        background-color: #5a6268 !important;
        border-color: #5a6268 !important;
        text-transform: lowercase !important;
        padding: 0.4rem 1rem !important;
        font-size: 0.9rem !important;
    }

    .fc-button:hover {
        background-color: #4e555b !important;
    }

    .fc-today-button {
        background-color: #6c757d !important;
    }

    .fc-prev-button,
    .fc-next-button {
        background-color: #2c3e50 !important;
        border-color: #2c3e50 !important;
    }

    .fc-day-sun .fc-col-header-cell-cushion {
        color: #007bff;
    }

    .fc-col-header-cell-cushion {
        color: #007bff;
        text-decoration: none;
    }

    .fc-daygrid-day-number {
        color: #007bff;
        text-decoration: none;
        font-size: 0.9rem;
    }

    .fc-day-other .fc-daygrid-day-number {
        color: #ccc;
    }

    .fc-daygrid-day:hover {
        background-color: #f8f9fa;
        cursor: pointer;
    }

    .fc-event {
        cursor: pointer;
        padding: 2px 4px;
        font-size: 0.8rem;
        border-radius: 4px;
    }
</style>

<?php
$listUrl = Url::to(['/trx/schedule/list']);
$createUrl = Url::to(['/trx/schedule/create']);
$deleteUrl = Url::to(['/trx/schedule/delete']);

$script = <<<JS
jQuery(function($) {
    console.log('Initializing Calendar...');
    var calendarEl = document.getElementById('calendar');
    var selectUser = $('#select-user');
    var selectShift = $('#select-shift');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'id', // Optional: Use Indonesian locale if available
        firstDay: 1, // Start week on Monday (Sen)
        headerToolbar: {
            left: 'title',
            right: 'today prev next'
        },
        buttonText: {
            today: 'hari ini',
            prev: 'prev',
            next: 'next'
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            var userId = selectUser.val();
            $.ajax({
                url: '{$listUrl}',
                dataType: 'json',
                data: {
                    id_user: userId,
                    start: fetchInfo.startStr,
                    end: fetchInfo.endStr
                },
                success: function(data) {
                    successCallback(data);
                },
                error: function() {
                    failureCallback();
                }
            });
        },
        dateClick: function(info) {
            var userId = selectUser.val();
            var shiftId = selectShift.val();

            if (!userId) {
                Swal.fire('Error', 'Please select a User first', 'error');
                return;
            }
            if (!shiftId) {
                Swal.fire('Error', 'Please select a Shift first', 'error');
                return;
            }

            $.ajax({
                url: '{$createUrl}',
                type: 'POST',
                data: {
                    id_user: userId,
                    id_shift: shiftId,
                    date: info.dateStr,
                    _csrf: yii.getCsrfToken()
                },
                success: function(response) {
                    if (response.success) {
                        calendar.refetchEvents();
                        Swal.fire('Success', response.message, 'success');
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                }
            });
        },
        eventClick: function(info) {
            Swal.fire({
                title: 'Delete Schedule?',
                text: "Are you sure you want to delete this schedule?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{$deleteUrl}',
                        type: 'POST',
                        data: {
                            id: info.event.id,
                            _csrf: yii.getCsrfToken()
                        },
                        success: function(response) {
                            if (response.success) {
                                info.event.remove();
                                Swal.fire('Deleted!', response.message, 'success');
                            } else {
                                Swal.fire('Error', response.message, 'error');
                            }
                        }
                    });
                }
            });
        }
    });

    calendar.render();

    // Trigger refetch when user changes
    selectUser.on('change', function() {
        calendar.refetchEvents();
    });
});
JS;
$this->registerJs($script, \yii\web\View::POS_END);
?>