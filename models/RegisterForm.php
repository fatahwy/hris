<?php

namespace app\models;

use app\models\master\Client;
use app\models\master\Company;
use Yii;
use yii\base\Model;

/**
 * RegisterForm is the model behind the register form.
 */
class RegisterForm extends Model
{
    public $nama_lengkap;
    public $nama_instansi;
    public $no_wa;
    public $email;
    public $password;
    public $confirm_password;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['nama_lengkap', 'nama_instansi', 'no_wa', 'email', 'password', 'confirm_password'], 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => '\app\models\master\Client', 'message' => 'Email sudah terdaftar di sistem.'],
            ['no_wa', 'string', 'min' => 10, 'max' => 15],
            ['password', 'string', 'min' => 6],
            ['confirm_password', 'compare', 'compareAttribute' => 'password', 'message' => 'Password tidak cocok.'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'nama_lengkap' => 'Nama Lengkap',
            'nama_instansi' => 'Nama Instansi',
            'no_wa' => 'No. WA',
            'email' => 'Email',
            'password' => 'Password',
            'confirm_password' => 'Confirm Password',
        ];
    }

    /**
     * Register a new user.
     * @return bool whether the user is registered successfully
     */
    public function register()
    {
        if ($this->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $flag = true;
                $mClient = new Client();
                $mClient->name = $this->nama_instansi;
                $mClient->email = $this->email;
                $mClient->is_active = 0;
                $mClient->token = Yii::$app->security->generateRandomString();
                $mClient->expired_at = date('Y-m-d H:i:s', strtotime('+7 days'));
                $flag = $flag && $mClient->save();

                $mCompany = new Company();
                $mCompany->name = $this->nama_instansi;
                $mCompany->id_client = $mClient->id_client;
                $flag = $flag && $mCompany->save();

                $mUser = new User();
                $mUser->id_client = $mClient->id_client;
                $mUser->id_company = $mCompany->id_company;
                $mUser->name = $this->nama_lengkap;
                $mUser->email = $this->email;
                $mUser->phone = $this->no_wa;
                $mUser->password = md5($this->password);
                $flag = $flag && $mUser->save();

                $mAuthAssignment = new AuthAssignment();
                $mAuthAssignment->item_name = 'Owner';
                $mAuthAssignment->user_id = $mUser->id_user;
                $flag = $flag && $mAuthAssignment->save();

                if ($flag) {
                    $transaction->commit();
                    return true;
                }
                // TODO: send email email and password login
            } catch (\Throwable $th) {
                $transaction->rollBack();
                throw $th;
            }
            return true;
        }
        return false;
    }
}