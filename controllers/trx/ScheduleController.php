<?php

namespace app\controllers\trx;

use app\controllers\BaseController;
use app\models\master\Account;
use app\models\master\Shift;
use app\models\trx\Schedule;
use Yii;
use yii\helpers\Json;
use yii\web\Response;

class ScheduleController extends BaseController
{
    public function actionIndex()
    {
        $users = Account::find()
            ->where(['id_company' => $this->id_company, 'status' => 1])
            ->all();

        $shifts = Shift::find()
            ->where(['id_company' => $this->id_company])
            ->all();

        return $this->render('index', [
            'users' => $users,
            'shifts' => $shifts,
        ]);
    }

    public function actionList($id_user = null, $start = null, $end = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $query = Schedule::find()
            ->where(['id_company' => $this->id_company])
            ->andFilterWhere(['id_user' => $id_user]);

        if ($start) {
            $query->andWhere(['>=', 'date', substr($start, 0, 10)]);
        }
        if ($end) {
            $query->andWhere(['<=', 'date', substr($end, 0, 10)]);
        }

        $schedules = $query->all();

        $events = [];
        foreach ($schedules as $schedule) {
            $shiftColor = $schedule->shift->color ?? '#3788d8';
            $events[] = [
                'id' => $schedule->id_schedule,
                'title' => $schedule->shift_name . ($id_user ? "" : " - " . $schedule->user->name),
                'start' => $schedule->date,
                'backgroundColor' => $shiftColor,
                'borderColor' => $shiftColor,
                'extendedProps' => [
                    'id_shift' => $schedule->id_shift,
                ]
            ];
        }

        return $events;
    }

    public function actionCreate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $post = Yii::$app->request->post();

        $id_user = $post['id_user'] ?? null;
        $id_shift = $post['id_shift'] ?? null;
        $date = $post['date'] ?? null;

        if (!$id_user || !$id_shift || !$date) {
            return ['success' => false, 'message' => 'Missing required parameters'];
        }

        // Check if user already has a schedule on this date
        $exists = Schedule::find()
            ->where(['id_user' => $id_user, 'date' => $date])
            ->exists();

        if ($exists) {
            return ['success' => false, 'message' => 'User already has a shift on this date'];
        }

        $shift = Shift::findOne($id_shift);
        if (!$shift) {
            return ['success' => false, 'message' => 'Shift not found'];
        }

        $model = new Schedule();
        $model->id_company = $this->id_company;
        $model->id_user = $id_user;
        $model->id_shift = $id_shift;
        $model->date = $date;
        $model->shift_name = $shift->name;
        $checkinDate = $date;
        $workhourStartDate = $date;
        $workhourEndDate = $date;

        if ($shift->checkin_start > $shift->workhour_start) {
            $checkinDate = date('Y-m-d', strtotime($date . ' -1 day'));
        }

        if ($shift->workhour_end < $shift->workhour_start) {
            $workhourEndDate = date('Y-m-d', strtotime($date . ' +1 day'));
        }

        $model->checkin_start = $checkinDate . ' ' . $shift->checkin_start;
        $model->workhour_start = $workhourStartDate . ' ' . $shift->workhour_start;
        $model->workhour_end = $workhourEndDate . ' ' . $shift->workhour_end;
        $model->status = 'Scheduled'; // Default status

        if ($model->save()) {
            return ['success' => true, 'message' => 'Schedule added successfully'];
        } else {
            return ['success' => false, 'message' => implode(', ', $model->getErrorSummary(true))];
        }
    }

    public function actionDelete()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = $_POST['id'] ?? null;
        if ($id) {
            $model = Schedule::findOne($id);
            if ($model && $model->delete()) {
                return ['success' => true, 'message' => 'Schedule deleted successfully'];
            }
        }
        return ['success' => false, 'message' => 'Failed to delete schedule'];
    }
}
