<?php

namespace app\queue;

use app\models\trx\Log;
use yii\base\BaseObject;
use yii\queue\JobInterface;

class CreateLog extends BaseObject implements JobInterface
{
    public $route;
    public $req;
    public $user;

    public function execute($queue)
    {
        Log::doLog($this->route, $this->req, $this->user);
    }
}