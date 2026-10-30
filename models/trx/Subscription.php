<?php

namespace app\models\trx;

use Yii;
use app\models\BaseModel;
use app\models\master\Client;
use app\models\master\Company;
use app\models\master\Paket;

/**
 * This is the model class for table "subscription".
 *
 * @property int $id_subscription
 * @property int $id_client
 * @property int $id_company
 * @property int $id_paket
 * @property int $durasi
 * @property float $price
 * @property int $status
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $uuid
 *
 * @property Client $client
 * @property Company $company
 * @property Paket $paket
 */
class Subscription extends BaseModel
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subscription';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_client', 'id_company', 'id_paket', 'durasi'], 'required'],
            [['id_client', 'id_company', 'id_paket', 'durasi', 'status'], 'integer'],
            [['price'], 'number'],
            [['created_at', 'updated_at', 'uuid'], 'safe'],
            [['id_client'], 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['id_client' => 'id_client']],
            [['id_company'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['id_company' => 'id_company']],
            [['id_paket'], 'exist', 'skipOnError' => true, 'targetClass' => Paket::class, 'targetAttribute' => ['id_paket' => 'id_paket']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_subscription' => 'ID Subscription',
            'id_client' => 'ID Client',
            'id_company' => 'ID Perusahaan',
            'id_paket' => 'ID Paket',
            'durasi' => 'Durasi (Hari)',
            'price' => 'Harga',
            'status' => 'Status',
            'created_at' => 'Waktu Mulai',
            'updated_at' => 'Terakhir Diperbarui',
        ];
    }

    /**
     * Gets query for [[Client]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Client::class, ['id_client' => 'id_client']);
    }

    /**
     * Gets query for [[Company]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::class, ['id_company' => 'id_company']);
    }

    /**
     * Gets query for [[Paket]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPaket()
    {
        return $this->hasOne(Paket::class, ['id_paket' => 'id_paket']);
    }
}
