<?php

namespace app\models;


use app\models\master\Account;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Yii;
use yii\web\IdentityInterface;

class User extends Account implements IdentityInterface
{

    public $authKey;
    public $accessToken;

    public static function generateToken($id, $exp = null, $secret = null)
    {
        // $token = [
        //     "iss" => "http://pranatahr.gerimisstudio.com",
        //     "aud" => "com.gerimisstudio.pranatahr",
        //     "uid" => $id,
        //     "iat" => time(),
        //     "nbf" => time(),
        //     "exp" => $exp ? $exp : time() + (1000 * 3600 * 24 * 7)
        // ];

        // return JWT::encode($token, $secret ? $secret : Yii::$app->params['secret'], 'HS256');
    }

    public static function validateToken($token, $secret = null)
    {
        // try {
        //     $payload = JWT::decode($token, new Key($secret ? $secret : Yii::$app->params['secret'], 'HS256'));
        //     if ($payload->uid === 0) {
        //         $statictoken = new User();
        //         $statictoken->idaccount = 0;
        //         return $statictoken;
        //     }
        //     $user = User::findIdentity($payload->uid);
        //     return $user;
        // } catch (Exception $ex) {
        //     return null;
        // }
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        $model = self::find()
            ->andWhere(['id_user' => $id, 'status' => 1])
            ->one();

        return $model;
    }

    public static function findIdentityWihtAccessToken($id)
    {
        $user = self::findIdentity($id);
        if ($user) {
            // $user->accessToken = self::generateToken($id);
        }
        return $user;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        if ($type == 'yii\filters\auth\HttpBearerAuth') {
            return User::validateToken($token);
        }

        return null;
    }

    /**
     * Finds user by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        $model = self::find()
            ->andWhere(['email' => $email, 'status' => 1])
            ->one();

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id_user;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return $this->password === md5($password);
    }
}