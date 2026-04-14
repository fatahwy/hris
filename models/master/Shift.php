<?php

namespace app\models\master;

use app\models\trx\Schedule;
use Yii;
use app\models\BaseModel;

/**
 * This is the model class for table "shift".
 *
 * @property int $id_shift
 * @property int $id_company
 * @property string $name
 * @property string $checkin_start
 * @property string $workhour_start
 * @property string $workhour_end
 * @property string|null $color
 * @property string|null $note
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Company $company
 * @property Schedule[] $schedules
 */
class Shift extends BaseModel
{

    public $id_client;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shift';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['color', 'note'], 'default', 'value' => null],
            [['id_company', 'name', 'checkin_start', 'workhour_start', 'workhour_end'], 'required'],
            [['id_company', 'id_client'], 'integer'],
            [['checkin_start', 'workhour_start', 'workhour_end', 'created_at', 'updated_at'], 'safe'],
            [['note'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['color'], 'string', 'max' => 10],
            [['id_company'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['id_company' => 'id_company']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_shift' => 'Id Shift',
            'id_company' => 'Id Company',
            'name' => 'Name',
            'checkin_start' => 'Checkin Start',
            'workhour_start' => 'Workhour Start',
            'workhour_end' => 'Workhour End',
            'color' => 'Color',
            'note' => 'Note',
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
     * Gets query for [[Schedules]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSchedules()
    {
        return $this->hasMany(Schedule::class, ['id_shift' => 'id_shift']);
    }

}