<?php

namespace app\commands;

use app\models\master\Client;
use app\models\User;
use yii\console\Controller;
use yii\db\Expression;
use app\helpers\DBHelper;
use app\models\trx\Schedule;

class CronController extends Controller
{

    public function actionIndex()
    {
        Schedule::updateAll(
            ['status' => Schedule::STATUS_ABSENT, 'status_present' => Schedule::STATUS_PRESENT_ABSENT],
            ['and', ['status' => Schedule::STATUS_SCHEDULED], ['<', 'workhour_end', DBHelper::now()]]
        );

        Client::deleteAll(
            ['and', ['is_active' => 0], ['confirm_at' => null], ['<', 'confirmation_sent_at', date('Y-m-d H:i:s', strtotime('-7 days'))]]
        );

        if (date('m-d') === '01-01' && (int)date('i') < 15) {
            User::updateAll([
                'remaining_leave' => new Expression('annual_leave_quota'),
            ]);
        }
    }

}
