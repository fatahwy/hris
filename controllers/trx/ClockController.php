<?php

namespace app\controllers\trx;

use app\helpers\DBHelper;
use Yii;
use app\controllers\BaseController;
use app\models\trx\Schedule;
use yii\web\Response;

class ClockController extends BaseController
{

    public function actionIndex()
    {
        $get = $this->findModel();
        $model = $get['model'];
        $type = $get['type'];

        if (!$model) {
            $model = Schedule::getActiveSchedule();
            if ($model) {
                Yii::$app->session->setFlash('warning', 'Anda sudah clock in.');
            } else {
                Yii::$app->session->setFlash('error', 'Anda tidak punya jadwal kerja.');
            }
            return $this->redirect(['/']);
        }

        $checkinStart = $model->checkin_start;
        $workhourEnd = $model->workhour_end;

        if ($model->checkout_datetime !== null) {
            Yii::$app->session->setFlash('warning', 'Anda sudah clock out.');
            return $this->redirect(['/']);
        }

        if ($type === 'in') {
            if ($model->checkin_datetime !== null) {
                Yii::$app->session->setFlash('warning', 'Anda sudah clock in.');
                return $this->redirect(['/']);
            }

            $nowStr = DBHelper::now();
            if ($nowStr < $checkinStart || $nowStr > $workhourEnd) {
                Yii::$app->session->setFlash('error', 'Anda tidak bisa clock in saat ini. Clock in bisa dilakukan dari ' . $checkinStart . ' and ' . $workhourEnd);
                return $this->redirect(['/']);
            }
        } else {
            if ($model->checkin_datetime === null) {
                Yii::$app->session->setFlash('error', 'Anda belum clock in.');
                return $this->redirect(['/']);
            }
        }

        return $this->render('clock', [
            'model' => $model,
            'type' => $type
        ]);
    }

    public function actionSave()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $get = $this->findModel();
        $model = $get['model'];
        $type = $get['type'];

        if (!$model) {
            Yii::$app->session->setFlash('error', 'Anda tidak punya jadwal kerja');
            return ['success' => false];
        }

        $photoData = Yii::$app->request->post('photo');
        $lat = Yii::$app->request->post('lat');
        $lng = Yii::$app->request->post('lng');

        if ($photoData) {
            $base64Data = preg_replace('#^data:image/\w+;base64,#i', '', $photoData);
            $img = base64_decode($base64Data);

            $dirPath = Yii::getAlias('@webroot/uploads/attendance');
            if (!is_dir($dirPath)) {
                mkdir($dirPath, 0777, true);
            }

            $fileName = $type . '_' . $model->id_schedule . '_' . time() . '.jpg';
            $filePath = $dirPath . '/' . $fileName;
            file_put_contents($filePath, $img);

            if ($type == 'in') {
                $model->checkin_datetime = date('Y-m-d H:i:s');
                $model->checkin_lat = strval($lat);
                $model->checkin_long = strval($lng);
                $model->checkin_photo = 'uploads/attendance/' . $fileName;
                $model->status = Schedule::STATUS_CHECKIN;
            } else {
                $model->checkout_datetime = date('Y-m-d H:i:s');
                $model->checkout_lat = strval($lat);
                $model->checkout_long = strval($lng);
                $model->checkout_photo = 'uploads/attendance/' . $fileName;
                $model->status = Schedule::STATUS_DONE;
            }

            if ($model->save(false)) {
                Yii::$app->session->setFlash('success', 'Clock ' . strtoupper($type) . ' Success!');
                return ['success' => true];
            }
        }

        return ['success' => false, 'message' => 'Failed to save attendance data.'];
    }

    protected function findModel()
    {
        $nowStr = DBHelper::now();
        $type = 'in';

        $model = Schedule::getAvailableSchedule();
        if (!$model) {
            $model = Schedule::getActiveScheduleToClockOut();
            if ($model) {
                $type = 'out';
            } else {
                $model = Schedule::getQueryByCompany()
                    ->andWhere(['id_user' => $this->user->id_user])
                    ->andWhere(['>', 'checkin_start', $nowStr])
                    ->orderBy(['date' => SORT_ASC])
                    ->one();
            }
        }

        return compact('model', 'type');
    }
}
