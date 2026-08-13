<?php

namespace app\controllers\json;

use app\models\trx\Schedule;
use app\controllers\BaseController;
use Yii;
use yii\web\Response;

/**
 * ScheduleController implements the CRUD actions for LeaveRequest model.
 */
class ScheduleController extends BaseController
{

    /**
     * AJAX endpoint to return schedule options for Select2 widget
     *
     * @param string|null $q
     * @param int|null $id
     * @return array
     */
    public function actionList($q = null, $id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($id !== null) {
            $schedule = Schedule::findOne($id);
            if ($schedule) {
                $workStart = date('H:i', strtotime($schedule->workhour_start));
                $workEnd = date('H:i', strtotime($schedule->workhour_end));
                return [
                    'results' => [
                        'id' => $schedule->id_schedule,
                        'text' => $schedule->date . ' - ' . $schedule->shift_name . ' (' . $workStart . ' - ' . $workEnd . ')',
                        'date' => $schedule->date,
                    ]
                ];
            }
            return ['results' => []];
        }

        $userId = $this->user->id_user;
        $query = Schedule::find()
            ->where(['id_user' => $userId]);

        if ($this->id_company) {
            $query->andWhere(['id_company' => $this->id_company]);
        }

        if ($q !== null && trim($q) !== '') {
            $query->andWhere([
                'OR',
                ['like', 'date', $q],
                ['like', 'shift_name', $q]
            ]);
        }

        $schedules = $query->orderBy(['date' => SORT_DESC])
            ->limit(30)
            ->all();

        $results = [];
        foreach ($schedules as $s) {
            $workStart = date('H:i', strtotime($s->workhour_start));
            $workEnd = date('H:i', strtotime($s->workhour_end));
            $results[] = [
                'id' => $s->id_schedule,
                'text' => $s->date . ' - ' . $s->shift_name . ' (' . $workStart . ' - ' . $workEnd . ')',
                'date' => $s->date,
            ];
        }

        return ['results' => $results];
    }

}
