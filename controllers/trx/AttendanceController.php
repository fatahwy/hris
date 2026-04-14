<?php

namespace app\controllers\trx;

use app\helpers\DBHelper;
use Yii;
use app\controllers\BaseController;
use app\models\trx\Schedule;
use app\models\trx\ScheduleSearch;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class AttendanceController extends BaseController
{

    public function actionIndex()
    {
        $searchModel = new ScheduleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $activeSchedule = Schedule::getActiveScheduleToClockOut();
        $availableSchedule = Schedule::getAvailableSchedule();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'activeSchedule' => $activeSchedule,
            'availableSchedule' => $availableSchedule,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionClock($id, $type)
    {
        if (!in_array($type, ['in', 'out'])) {
            throw new NotFoundHttpException('Invalid clock type.');
        }

        $model = $this->findModel($id);

        $nowStr = DBHelper::now();
        $checkinStart = $model->checkin_start;
        $workhourEnd = $model->workhour_end;

        if ($type === 'in') {
            if ($nowStr < $checkinStart || $nowStr > $workhourEnd) {
                Yii::$app->session->setFlash('error', 'Cannot clock in at this time. Allowed between ' . $checkinStart . ' and ' . $workhourEnd);
                return $this->redirect(['index']);
            }
            if ($model->checkin_datetime !== null) {
                Yii::$app->session->setFlash('warning', 'You have already checked in.');
                return $this->redirect(['index']);
            }
        } else {
            if ($model->checkin_datetime === null) {
                Yii::$app->session->setFlash('error', 'You have not checked in yet.');
                return $this->redirect(['index']);
            }
            if ($model->checkout_datetime !== null) {
                Yii::$app->session->setFlash('warning', 'You have already checked out.');
                return $this->redirect(['index']);
            }
        }

        return $this->render('clock', [
            'model' => $model,
            'type' => $type
        ]);
    }

    public function actionSaveClock($id, $type)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = $this->findModel($id);

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
                $model->status = 'Checkin';
            } else {
                $model->checkout_datetime = date('Y-m-d H:i:s');
                $model->checkout_lat = strval($lat);
                $model->checkout_long = strval($lng);
                $model->checkout_photo = 'uploads/attendance/' . $fileName;
                $model->status = 'Done';
            }

            if ($model->save(false)) {
                Yii::$app->session->setFlash('success', 'Clock ' . strtoupper($type) . ' Success!');
                return ['success' => true];
            }
        }

        return ['success' => false, 'message' => 'Failed to save attendance data.'];
    }

    protected function findModel($id)
    {
        $id_user = Yii::$app->user->identity->id_user ?? 0;
        if (($model = Schedule::findOne(['id_schedule' => $id, 'id_user' => $id_user])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
