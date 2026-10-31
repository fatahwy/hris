<?php

namespace app\controllers;

use app\helpers\DBHelper;
use app\helpers\RoleHelper;
use Yii;
use app\models\master\Account;
use app\models\trx\LeaveRequest;
use app\models\trx\Payroll;
use app\models\trx\Schedule;

class DashboardController extends BaseController
{

    /**
     * Displays homepage/Dashboard.
     *
     * @return string
     */
    public function actionIndex()
    {
        $id_company = $this->id_company;

        $dateRange = Yii::$app->request->get('date_range');
        $startDate = null;
        $endDate = null;
        if ($dateRange && is_string($dateRange) && strpos($dateRange, ' - ') !== false) {
            $dates = explode(' - ', $dateRange);
            if (isset($dates[0], $dates[1]) && strtotime(trim($dates[0])) && strtotime(trim($dates[1]))) {
                $startDate = trim($dates[0]);
                $endDate = trim($dates[1]);
            }
        }

        if (!$startDate || !$endDate) {
            $startDate = date('Y-m-01');
            $endDate = date('Y-m-t');
            $dateRange = "$startDate - $endDate";
        }

        $user = $this->user;
        $isAllUser = RoleHelper::allUser();
        $isApprovalPayroll = RoleHelper::approvalPayroll();
        $isApprovalLeave = RoleHelper::approvalLeave();

        $totalEmployees = Account::find()->where(['id_company' => $id_company, 'status' => 1])->count();

        $presentInPeriod = Schedule::find()
            ->where(['id_company' => $id_company])
            ->andWhere(['between', 'date', $startDate, $endDate])
            ->andWhere(['not', ['checkin_datetime' => null]])
            ->andFilterWhere(['id_user' => $isAllUser ? null : $user->id_user])
            ->count();

        $pendingApprovals = LeaveRequest::find()
            ->innerJoinWith('user')
            ->where(['user.id_company' => $id_company, 'leave_request.status' => LeaveRequest::STATUS_PENDING])
            ->andFilterWhere(['leave_request.id_user' => $isApprovalLeave ? null : $user->id_user])
            ->count();

        $monthlyPayroll = Payroll::find()
            ->where(['id_company' => $id_company, 'status' => Payroll::STATUS_APPROVE])
            ->andWhere(['between', 'user_approve_at', $startDate, $endDate])
            ->andFilterWhere(['id_user' => $isApprovalPayroll ? null : $user->id_user])
            ->sum('net_salary') ?: 0;

        $recentAttendances = Schedule::find()
            ->with(['user'])
            ->where(['id_company' => $id_company])
            ->andWhere(['between', 'date', $startDate, $endDate])
            ->andWhere(['<', 'checkin_start', DBHelper::now()])
            ->andFilterWhere(['schedule.id_user' => $isAllUser ? null : $user->id_user])
            ->orderBy(['date' => SORT_DESC, 'checkin_datetime' => SORT_DESC])
            ->limit(10)
            ->all();

        return $this->render('index', [
            'totalEmployees' => $totalEmployees,
            'presentInPeriod' => $presentInPeriod,
            'pendingApprovals' => $pendingApprovals,
            'monthlyPayroll' => $monthlyPayroll,
            'recentAttendances' => $recentAttendances,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'dateRange' => $dateRange,
        ]);
    }

}
