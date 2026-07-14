<?php

namespace app\controllers\trx;

use app\controllers\BaseController;
use app\helpers\GeneralHelper;
use app\models\master\Account;
use app\models\master\Shift;
use app\models\trx\Schedule;
use Yii;
use yii\web\Response;

class ScheduleController extends BaseController
{
    public function actionIndex()
    {
        $users = Account::getQueryByCompany('user')
            ->andWhere(['status' => 1])
            ->all();

        return $this->render('index', [
            'users' => $users,
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
            $labelOvertime = $schedule->is_overtime ? '[Lembur] ' : '';

            $events[] = [
                'id' => $schedule->id_schedule,
                'title' => $labelOvertime . $schedule->shift_name . ($id_user ? "" : " - " . $schedule->user->name),
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
            ->where(['id_user' => $id_user, 'date' => $date, 'id_shift' => $id_shift])
            ->exists();

        if ($exists) {
            return ['success' => false, 'message' => 'Pegawai sudah memiliki jadwal pada tanggal ini'];
        }

        $shift = Shift::findOne($id_shift);
        if (!$shift) {
            return ['success' => false, 'message' => 'Jadwal kerja tidak ditemukan'];
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
        $model->status = Schedule::STATUS_SCHEDULED; // Default status

        if ($model->save()) {
            return ['success' => true, 'message' => 'Jadwal berhasil ditambahkan'];
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
            if ($model) {
                if ($model->checkin_datetime != null || $model->checkout_datetime != null) {
                    return ['success' => false, 'message' => 'Pegawai sudah melakukan absensi'];
                } else {
                    $model->delete();
                    return ['success' => true, 'message' => 'Jadwal berhasil dihapus'];
                }
            }
        }
        return ['success' => false, 'message' => 'Gagal menghapus jadwal'];
    }

    public function actionOvertime()
    {
        $users = Account::getQueryByCompany('user')
            ->andWhere(['status' => 1])
            ->indexBy('id_user')
            ->all();
        $shifts = Shift::getList();

        $model = new Schedule();
        $model->id_company = $this->id_company;

        if (Yii::$app->request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $post = Yii::$app->request->post();

            $model->load($post);

            $isPerHour = !empty($model->workhour_start) && !empty($model->workhour_end);

            if ($isPerHour) { // is per hour
                $mShift = Shift::findOne(GeneralHelper::ID_OVERTIME);

                $model->workhour_start = date('Y-m-d H:i:s', strtotime($model->workhour_date . ' ' . $post['workhour_start']));
                $model->workhour_end = date('Y-m-d H:i:s', strtotime($model->workhour_start) . " +" . $post['workhour_end'] . " hours");
                $model->checkin_start = date('Y-m-d 00:00:00', strtotime($model->workhour_start));
            } else { // is long shift
                $mShift = Shift::getQueryByCompany()
                    ->andWhere(['id_shift' => $model->id_shift])
                    ->one();

                if (empty($shifts[$model->id_shift]) || !$mShift) {
                    return ['success' => false, 'message' => 'Shift tidak ditemukan'];
                }

                $checkinDate = $model->date;
                $workhourStartDate = $model->date;
                $workhourEndDate = $model->date;

                if ($mShift->checkin_start > $mShift->workhour_start) {
                    $checkinDate = date('Y-m-d', strtotime($model->date . ' -1 day'));
                }
                if ($mShift->workhour_end < $mShift->workhour_start) {
                    $workhourEndDate = date('Y-m-d', strtotime($model->date . ' +1 day'));
                }

                $model->checkin_start = $checkinDate . ' ' . $mShift->checkin_start;
                $model->workhour_start = $workhourStartDate . ' ' . $mShift->workhour_start;
                $model->workhour_end = $workhourEndDate . ' ' . $mShift->workhour_end;
            }

            if (empty($users[$model->id_user])) {
                $model->id_user = -1;
            }

            $model->shift_name = $mShift->name;
            $model->is_overtime = true;
            $model->id_shift = $mShift->id_shift;
            $model->status = Schedule::STATUS_SCHEDULED; // Default status

            if ($model->save()) {
                return ['success' => true, 'message' => 'Pengajuan lembur berhasil disimpan'];
            } else {
                return ['success' => false, 'message' => implode(', ', $model->getErrorSummary(true))];
            }
        }

        return $this->render('overtime', [
            'model' => $model,
            'users' => $users,
            'shifts' => $shifts,
        ]);
    }
}
