<?php

namespace app\queue;

use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;

class SendEmailJob extends BaseObject implements JobInterface
{
    public $sendTo;
    public $subject;
    public $body;

    public function execute($queue)
    {
        $params = Yii::$app->params;
        Yii::$app->mailer->compose()
            ->setFrom($params['senderEmail'])
            ->setTo($this->sendTo)
            ->setSubject($this->subject)
            // ->setTextBody('Plain text content')
            ->setHtmlBody($this->body)
            ->send();
    }
}
