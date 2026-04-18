<?php

namespace app\models\master;

use app\helpers\RoleHelper;
use Yii;
use yii\helpers\ArrayHelper;
use app\models\BaseModel;
use app\models\trx\Payroll;
use app\models\trx\Schedule;
use app\models\User;

/**
 * This is the model class for table "company".
 *
 * @property int $id_company
 * @property int $id_client
 * @property string $name
 * @property string|null $address
 * @property int $status
 * @property int $max_user
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Client $client
 * @property Department[] $departments
 * @property LeaveType[] $leaveTypes
 * @property Payroll[] $payrolls
 * @property Position[] $positions
 * @property Schedule[] $schedules
 * @property Shift[] $shifts
 * @property Account[] $users
 * @property Account[] $users0
 */
class Company extends BaseModel
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'company';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['address'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 1],
            [['max_user'], 'default', 'value' => 10],
            [['id_client', 'name'], 'required'],
            [['id_client', 'status', 'max_user'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'address'], 'string', 'max' => 255],
            [['id_client'], 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['id_client' => 'id_client']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_company' => 'Id Company',
            'id_client' => 'Client',
            'name' => 'Nama',
            'address' => 'Alamat',
            'status' => 'Status',
            'max_user' => 'Maximal User',
            'created_at' => 'Tgl Buat',
            'updated_at' => 'Tgl Update',
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
     * Gets query for [[Departments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDepartments()
    {
        return $this->hasMany(Department::class, ['id_company' => 'id_company']);
    }

    /**
     * Gets query for [[LeaveTypes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLeaveTypes()
    {
        return $this->hasMany(LeaveType::class, ['id_company' => 'id_company']);
    }

    /**
     * Gets query for [[Payrolls]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPayrolls()
    {
        return $this->hasMany(Payroll::class, ['id_company' => 'id_company']);
    }

    /**
     * Gets query for [[Positions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPositions()
    {
        return $this->hasMany(Position::class, ['id_company' => 'id_company']);
    }

    /**
     * Gets query for [[Schedules]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSchedules()
    {
        return $this->hasMany(Schedule::class, ['id_company' => 'id_company']);
    }

    /**
     * Gets query for [[Shifts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getShifts()
    {
        return $this->hasMany(Shift::class, ['id_company' => 'id_company']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['id_client' => 'id_client']);
    }

    /**
     * Gets query for [[Users0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers0()
    {
        return $this->hasMany(User::class, ['id_company' => 'id_company']);
    }

    public static function getList($id_client = null)
    {
        if (RoleHelper::isSuper()) {
            if (!$id_client) {
                return [];
            }
        }

        $models = self::find()
            ->andFilterWhere(['id_client' => $id_client])
            ->all();

        return ArrayHelper::map($models, 'id_company', 'name');
    }

}