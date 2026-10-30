<?php

namespace app\controllers\trx;

use app\helpers\DBHelper;
use app\helpers\GeneralHelper;
use app\helpers\RoleHelper;
use app\models\master\LeaveType;
use app\models\trx\LeaveRequest;
use app\models\trx\Schedule;
use app\models\trx\search\LeaveRequestSearch;
use app\controllers\BaseController;
use Yii;
use yii\bootstrap5\Html;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * LeaveRequestController implements the CRUD actions for LeaveRequest model.
 */
class LeaveRequestController extends BaseController
{

    /**
     * Lists all LeaveRequest models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new LeaveRequestSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single LeaveRequest model.
     * @param string $id_leave_request Id Leave Request
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id_leave_request)
    {
        $model = $this->findModel($id_leave_request);
        $permissionOnDuty = in_array((int) $model->leaveType->id_leave_type, LeaveType::P_ON_DUTY);

        return $this->render('view', [
            'model' => $model,
            'permissionOnDuty' => $permissionOnDuty,
        ]);
    }

    public function actionApproval($id_leave_request)
    {
        if (!RoleHelper::approvalLeave()) {
            throw new ForbiddenHttpException('You are not allowed to approve leaves.');
        }
        $model = $this->findModel($id_leave_request);
        $model->scenario = 'approval';

        $temp = clone $model;

        if ($temp->load($this->request->post())) {
            $model->status = $temp->status;
            if ($model->status == LeaveRequest::STATUS_PENDING) {
                $model->id_approver = null;
                $model->approve_reason = null;
                $model->approve_at = null;
            } else {
                $model->id_approver = $this->user->id_user;
                $model->approve_reason = $temp->approve_reason;
                $model->approve_at = DBHelper::now();

                $mLeaveType = $model->leaveType;
                $permissionOnDuty = in_array((int) $model->id_leave_type, LeaveType::P_ON_DUTY);
                if (!$permissionOnDuty && $mLeaveType->category == LeaveType::CATEGORY_LEAVE) {
                    $mSchedule = Schedule::find()
                        ->where(['id_user' => $model->id_user])
                        ->andWhere(['>=', 'date', $model->start_date])
                        ->andWhere(['<=', 'date', $model->end_date])
                        ->all();
                    if ($mSchedule) {
                        foreach ($mSchedule as $value) {
                            $value->status_present = Schedule::STATUS_PRESENT_PAID_LEAVE;
                            $value->save();
                        }
                    }
                }
            }

            if ($model->save()) {
                GeneralHelper::flashSucceed();
                return $this->redirect(['index']);
            }
            GeneralHelper::flashFailed(Html::errorSummary($model));
        }

        return $this->render('process', [
            'model' => $model,
            'isApproval' => true,
        ]);
    }

    public function actionProcess($id_leave_request = null)
    {
        $model = null;
        if ($id_leave_request) {
            $model = $this->findModel($id_leave_request);
            if (!$model->isStatusPending() || $model->id_user !== $this->user->id_user) {
                throw new ForbiddenHttpException('Permohonan cuti/izin ini tidak dapat diubah.');
            }
        }
        if (!$model) {
            $model = new LeaveRequest();
            $model->load($this->request->get());
        }

        $permissionOnDuty = in_array((int) $model->id_leave_type, LeaveType::P_ON_DUTY);

        if ($model->load($this->request->post())) {
            $permissionOnDuty = in_array((int) $model->id_leave_type, LeaveType::P_ON_DUTY);

            $startTime = '00:00:00';
            $endTime = '23:59:59';

            if ($permissionOnDuty) {
                $mSchedule = Schedule::findOne(['id_schedule' => $model->id_schedule, 'id_user' => $this->user->id_user]);
                if ($mSchedule) {
                    $startTime = $model->start_date;
                    $endTime = $model->end_date;
                    $model->start_date = date('Y-m-d H:i:00', strtotime($mSchedule->date . ' ' . $startTime));
                    $model->end_date = date('Y-m-d H:i:00', strtotime($mSchedule->date . ' ' . $endTime));
                }
            } else {
                $model->id_schedule = null;
            }

            $model->id_user = $this->user->id_user;
            $model->id_approver = null;
            $model->approve_reason = null;
            $model->approve_at = null;

            // Handle file attachment
            $oldAttachment = $id_leave_request ? $this->findModel($id_leave_request)->attachment : null;
            $attachmentFile = \yii\web\UploadedFile::getInstance($model, 'attachment');
            if ($attachmentFile) {
                $ext = strtolower($attachmentFile->extension);
                $allowedExts = ['png', 'jpg', 'jpeg', 'pdf'];
                if (!in_array($ext, $allowedExts)) {
                    GeneralHelper::flashFailed('Format lampiran tidak valid. Hanya png, jpg, jpeg, pdf yang diizinkan.');
                    return $this->render('process', compact('model', 'permissionOnDuty'));
                }
                if ($attachmentFile->size > 5 * 1024 * 1024) {
                    GeneralHelper::flashFailed('Ukuran lampiran maksimal 5MB.');
                    return $this->render('process', compact('model', 'permissionOnDuty'));
                }
                $uploadDir = Yii::getAlias('@webroot/uploads/leave_request');
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $fileName = 'leave_' . time() . '_' . uniqid() . '.' . $ext;
                $filePath = $uploadDir . '/' . $fileName;
                if ($attachmentFile->saveAs($filePath)) {
                    $model->attachment = 'uploads/leave_request/' . $fileName;
                }
            } elseif ($oldAttachment) {
                $model->attachment = $oldAttachment;
            }

            if ($model->save()) {
                GeneralHelper::flashSucceed();
                return $this->redirect(['view', 'id_leave_request' => $model->id_leave_request]);
            }
            GeneralHelper::flashFailed(Html::errorSummary($model));
        }

        if ($permissionOnDuty) {
            if ($model->start_date) {
                $model->start_date = date('H:i', strtotime($model->start_date));
            }
            if ($model->end_date) {
                $model->end_date = date('H:i', strtotime($model->end_date));
            }
        }

        return $this->render('process', [
            'permissionOnDuty' => $permissionOnDuty,
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing LeaveRequest model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id_leave_request Id Leave Request
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id_leave_request)
    {
        $model = $this->findModel($id_leave_request);
        if (!$model->isStatusPending() || $model->id_user !== $this->user->id_user) {
            throw new ForbiddenHttpException('Permohonan cuti/izin ini tidak dapat dihapus.');
        }

        $model->delete();
        GeneralHelper::flashSucceed('Permohonan berhasil dihapus.');

        return $this->redirect(['index']);
    }

    protected function findModel($id_leave_request)
    {
        $query = LeaveRequest::find()
            ->innerJoinWith(['user'])
            ->where(['leave_request.id_leave_request' => $id_leave_request, 'user.id_company' => $this->id_company]);

        if (!RoleHelper::approvalLeave()) {
            $query->andWhere(['leave_request.id_user' => $this->user->id_user]);
        }

        if (($model = $query->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
