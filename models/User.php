<?php

namespace app\models;

use app\models\master\Account;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Yii;
use yii\web\IdentityInterface;

/**
 * User represents identity model extending Account.
 *
 * Tab 1: Data Pribadi
 * @property string|null $nik
 * @property string|null $gender
 * @property string|null $birth_place
 * @property string|null $birth_date
 * @property string|null $marital_status
 * @property string|null $religion
 * @property string|null $education
 * @property string|null $photo
 * @property string|null $ktp_address
 * @property int|null $is_domicile_same_ktp
 * @property string|null $domicile_address
 * @property string|null $emergency_contact_name
 * @property string|null $emergency_contact_relation
 * @property string|null $emergency_contact_phone
 *
 * Tab 2: Pekerjaan
 * @property string|null $employment_status
 * @property int|null $id_manager
 * @property string|null $work_location
 * @property string|null $contract_end_date
 * @property string|null $leave_entitlement_date
 * @property string|null $resign_date
 * @property string|null $resign_reason
 * @property int|null $annual_leave_quota
 * @property int|null $remaining_leave
 *
 * Tab 3: Payroll & Pajak
 * @property string|null $npwp
 * @property string|null $tax_method
 * @property string|null $bank_name
 * @property string|null $bank_account_number
 * @property string|null $bank_account_holder
 *
 * Tab 4: BPJS
 * @property string|null $bpjs_kesehatan_number
 * @property string|null $bpjs_ketenagakerjaan_number
 * @property int|null $bpjs_kesehatan
 * @property int|null $bpjs_jht
 * @property int|null $bpjs_jp
 * @property int|null $bpjs_jkk
 * @property int|null $bpjs_jkm
 */
class User extends Account implements IdentityInterface
{
    public $authKey;
    public $accessToken;

    public static function generateToken($id, $exp = null, $secret = null)
    {
        // JWT generation placeholder
    }

    public static function validateToken($token, $secret = null)
    {
        // JWT validation placeholder
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
