<?php

namespace app\models;

use app\models\master\Client;
use app\models\master\Company;
use app\models\master\Paket;
use app\models\trx\Subscription;
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
    public $id_paket;
    public $referensi;

    /** @var Client|null */
    public $client;
    /** @var Paket|null */
    public $paket;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['nama_lengkap', 'nama_instansi', 'no_wa', 'email', 'password', 'confirm_password', 'id_paket'], 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => '\app\models\master\Client', 'message' => 'Email sudah terdaftar di sistem.'],
            ['no_wa', 'string', 'min' => 10, 'max' => 15],
            ['password', 'string', 'min' => 6],
            ['confirm_password', 'compare', 'compareAttribute' => 'password', 'message' => 'Password tidak cocok.'],
            ['id_paket', 'integer'],
            ['id_paket', 'exist', 'targetClass' => '\app\models\master\Paket', 'targetAttribute' => 'id_paket', 'message' => 'Paket pilihan tidak valid.'],
            [['referensi'], 'trim'],
            [['referensi'], 'default', 'value' => null],
            [['referensi'], 'string', 'max' => 255],
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
            'id_paket' => 'Pilihan Paket Langganan',
            'referensi' => 'Referensi (Marketing)',
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

                $mPaket = Paket::findOne($this->id_paket);
                if (!$mPaket) {
                    $mPaket = Paket::findOne(['kode' => 'TRIAL']) ?: Paket::find()->one();
                }

                $isTrial = $mPaket->kode === 'TRIAL';
                // Hitung durasi dan expired_at berdasarkan paket
                if ($isTrial || $mPaket->durasi_hari == 14) {
                    $expiredAt = date('Y-m-d H:i:s', strtotime('+14 days'));
                } elseif ($mPaket->durasi_satuan === 'bulan' || $mPaket->durasi_hari == 30) {
                    $expiredAt = date('Y-m-d H:i:s', strtotime('+1 month'));
                } elseif ($mPaket->durasi_satuan === 'tahun' || $mPaket->durasi_hari == 365) {
                    $expiredAt = date('Y-m-d H:i:s', strtotime('+1 year'));
                } else {
                    $expiredAt = date('Y-m-d H:i:s', strtotime("+{$mPaket->durasi_hari} days"));
                }

                $mClient = new Client();
                $mClient->name = $this->nama_instansi;
                $mClient->email = $this->email;
                $mClient->referensi = $this->referensi;
                $mClient->is_active = 0;
                $mClient->token = Yii::$app->security->generateRandomString();
                $flag = $flag && $mClient->save();

                $mCompany = new Company();
                $mCompany->name = $this->nama_instansi;
                $mCompany->id_client = $mClient->id_client;
                $mCompany->max_user = $mPaket->max_user;
                $mCompany->status = 0;
                $mCompany->expired_at = $expiredAt;
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

                // Input ke table subscription untuk riwayat company subscription
                $mSubscription = new Subscription();
                $mSubscription->id_client = $mClient->id_client;
                $mSubscription->id_company = $mCompany->id_company;
                $mSubscription->id_paket = $mPaket->id_paket;
                $mSubscription->durasi = (int) $mPaket->durasi_hari;
                $mSubscription->price = $mPaket->price;
                $mSubscription->status = $isTrial ? Subscription::STATUS_ACTIVE : Subscription::STATUS_INACTIVE;
                $mSubscription->created_at = date('Y-m-d H:i:s');
                $mSubscription->updated_at = date('Y-m-d H:i:s');
                $flag = $flag && $mSubscription->save();

                if ($flag) {
                    $transaction->commit();
                    $this->client = $mClient;
                    $this->paket = $mPaket;
                    return true;
                } else {
                    $transaction->rollBack();
                    return false;
                }
            } catch (\Throwable $th) {
                $transaction->rollBack();
                throw $th;
            }
        }
        return false;
    }
}
