<?php

namespace app\models\master;

use Yii;
use app\models\BaseModel;

/**
 * This is the model class for table "position".
 *
 * @property int $id_position
 * @property int $id_company
 * @property string $name
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Company $company
 * @property Account[] $users
 */
class Position extends BaseModel
{

    public $id_client;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'position';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_company', 'name'], 'required'],
            [['id_company', 'id_client'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['id_company'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['id_company' => 'id_company']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_position' => 'Id Position',
            'id_company' => 'Id Company',
            'name' => 'Name',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
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
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(Account::class, ['id_position' => 'id_position']);
    }

}