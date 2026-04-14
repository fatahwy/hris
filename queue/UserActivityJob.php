<?php

namespace app\queue;

use app\helpers\DBHelper;
use yii\base\BaseObject;
use yii\queue\JobInterface;

class UserActivityJob extends BaseObject implements JobInterface
{
    public $user;
    public $path;

    public function execute($queue)
    {
        $user = $this->user;
        $user->online = 1;
        $user->path_info = $this->path ?? '';
        $user->activity_time = DBHelper::now();
        $user->save(false);
    }
}
