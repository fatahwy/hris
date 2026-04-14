<?php

namespace app\models\master;

use Yii;
use yii\helpers\ArrayHelper;
use app\models\BaseModel;

/**
 * This is the model class for table "client".
 *
 * @property int $id_client
 * @property string $name
 * @property string|null $expired_at
 * @property string $email
 * @property int $is_active
 * @property string|null $token
 * @property string|null $confirmation_sent_at
 * @property string|null $confirm_at
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Company[] $companies
 */
class Client extends BaseModel
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'client';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['expired_at'], 'default', 'value' => null],
            [['name', 'email'], 'required'],
            [['email'], 'unique'],
            [['token'], 'string'],
            [['is_active'], 'integer'],
            [['confirmation_sent_at', 'confirm_at', 'expired_at', 'created_at', 'updated_at'], 'safe'],
            [['name', 'email'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_client' => 'Id Client',
            'name' => 'Name',
            'expired_at' => 'Expired At',
            'email' => 'Email',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Companies]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompanies()
    {
        return $this->hasMany(Company::class, ['id_client' => 'id_client']);
    }

    public static function getList()
    {
        $models = self::find()
            ->all();

        return ArrayHelper::map($models, 'id_client', 'name');
    }

}