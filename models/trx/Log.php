<?php

namespace app\models\trx;

use app\models\master\Account;
use Yii;

/**
 * This is the model class for table "log".
 *
 * @property int $id_log
 * @property string $action
 * @property string $url
 * @property string $ip
 * @property string|null $data
 * @property int $id_user
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Account $user
 */
class Log extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['data'], 'default', 'value' => null],
            [['action', 'url', 'ip', 'id_user'], 'required'],
            [['data'], 'string'],
            [['id_user'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['action', 'url', 'ip'], 'string', 'max' => 255],
            [['id_user'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['id_user' => 'id_user']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_log' => 'Id Log',
            'action' => 'Action',
            'url' => 'Url',
            'ip' => 'Ip',
            'data' => 'Data',
            'id_user' => 'Id User',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Account]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Account::class, ['id_user' => 'id_user']);
    }

    public static function doLog($route, $req, $user)
    {
        $loginForm = $req->post('LoginForm');
        if (empty($user->id_user) && !empty($loginForm) && !empty($loginForm['email'])) {
            $user = Account::findOne(['email' => $loginForm['email']]);
        }
        $log = new Log();
        $log->action = $route;
        $log->url = $req->getAbsoluteUrl();
        $log->ip = $req->getUserIP();
        $log->id_user = $user->id_user ?? null;

        $postData = $req->post();
        if (is_array($postData)) {
            array_walk_recursive($postData, function (&$value, $key) {
                if (preg_match('/(password|token|secret|pin|auth_key)/i', $key)) {
                    $value = '********';
                }
            });
        }

        $log->data = json_encode([$postData, $req->get(), $_FILES]);
        $log->save(false);
    }
}