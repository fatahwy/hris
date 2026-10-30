<?php

namespace app\models\master;

use Yii;
use app\models\BaseModel;
use app\models\trx\Subscription;

/**
 * This is the model class for table "paket".
 *
 * @property int $id_paket
 * @property string $nama_paket
 * @property string $kode
 * @property string $tier
 * @property int $max_user
 * @property int $durasi_hari
 * @property string $durasi_satuan
 * @property float $price
 * @property int $is_active_immediately
 * @property int $status
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $uuid
 *
 * @property Subscription[] $subscriptions
 */
class Paket extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'paket';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nama_paket', 'kode', 'tier', 'max_user', 'durasi_hari', 'durasi_satuan'], 'required'],
            [['max_user', 'durasi_hari', 'is_active_immediately', 'status'], 'integer'],
            [['price'], 'number'],
            [['created_at', 'updated_at', 'uuid'], 'safe'],
            [['nama_paket'], 'string', 'max' => 100],
            [['kode', 'tier'], 'string', 'max' => 50],
            [['durasi_satuan'], 'string', 'max' => 20],
            [['kode'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_paket' => 'ID Paket',
            'nama_paket' => 'Nama Paket',
            'kode' => 'Kode Paket',
            'tier' => 'Tier',
            'max_user' => 'Maksimal User',
            'durasi_hari' => 'Durasi (Hari)',
            'durasi_satuan' => 'Satuan Durasi',
            'price' => 'Harga',
            'is_active_immediately' => 'Langsung Aktif',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Subscriptions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriptions()
    {
        return $this->hasMany(Subscription::class, ['id_paket' => 'id_paket']);
    }

    /**
     * Get label with pricing and duration for dropdowns.
     *
     * @return string
     */
    public function getDropdownLabel()
    {
        $priceText = $this->price > 0 ? 'Rp ' . number_format($this->price, 0, ',', '.') : 'Gratis';
        $activeText = $this->is_active_immediately ? '' : '';
        return "{$this->nama_paket} - Maks {$this->max_user} User - {$priceText}{$activeText}";
    }

    /**
     * List active packages as key-value pairs for dropdowns.
     *
     * @return array
     */
    public static function getDropdownList()
    {
        $models = self::find()->where(['status' => 1])->orderBy(['id_paket' => SORT_ASC])->all();
        $list = [];
        foreach ($models as $model) {
            $list[$model->id_paket] = $model->getDropdownLabel();
        }
        return $list;
    }
}
