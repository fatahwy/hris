<?php

namespace app\controllers\trx;

use app\helpers\DBHelper;
use app\helpers\GeneralHelper;
use app\helpers\RoleHelper;
use app\models\trx\LeaveRequest;
use app\models\trx\search\LeaveRequestSearch;
use app\controllers\BaseController;
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
        return $this->render('view', [
            'model' => $this->findModel($id_leave_request),
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
        }
        if (!$model) {
            $model = new LeaveRequest();
        }

        if ($model->load($this->request->post())) {
            $model->id_user = $this->user->id_user;
            $model->id_approver = null;
            $model->approve_reason = null;
            $model->approve_at = null;

            if ($model->save()) {
                GeneralHelper::flashSucceed();
                return $this->redirect(['view', 'id_leave_request' => $model->id_leave_request]);
            }
            GeneralHelper::flashFailed(Html::errorSummary($model));
        }

        return $this->render('process', [
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
        $this->findModel($id_leave_request)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($id_leave_request)
    {
        if (($model = LeaveRequest::findOne(['id_leave_request' => $id_leave_request])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
