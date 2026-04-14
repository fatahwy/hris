<?php

namespace app\commands;

use app\models\master\Client;
use Yii;
use yii\console\Controller;
use yii\db\Query;
use app\helpers\DBHelper;
use app\models\trx\Schedule;

class CronController extends Controller
{

    public function actionIndex()
    {
        Schedule::updateAll(
            ['status' => 'Absent'],
            ['and', ['status' => 'Scheduled'], ['<', 'workhour_end', DBHelper::now()]]
        );

        Client::deleteAll(
            ['and', ['is_active' => 0], ['confirm_at' => null], ['<', 'confirmation_sent_at', date('Y-m-d H:i:s', strtotime('-7 days'))]]
        );
    }

}
