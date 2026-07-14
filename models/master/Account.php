<?php

namespace app\models\master;

use app\helpers\GeneralHelper;
use Yii;
use app\models\AuthAssignment;
use app\models\trx\LeaveRequest;
use app\models\trx\Payroll;
use app\models\trx\Log;
use app\models\trx\Schedule;
use app\models\BaseModel;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "user".
 *
 * @property int $id_user
 * @property int $id_client
 * @property int $id_company
 * @property string $name
 * @property string $email
 * @property string $password
 * @property int $status
 * @property string|null $join_date
 * @property string|null $employee_code
 * @property string|null $phone
 * @property int|null $id_department
 * @property int|null $id_position
 * @property int|null $basic_salary
 * @property int|null $hourly_rate
 * @property int $is_online
 * @property string|null $token
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Company $client
 * @property Company $company
 * @property Department $department
 * @property LeaveRequest[] $leaveRequests
 * @property LeaveRequest[] $leaveRequests0
 * @property Log[] $logs
 * @property Payroll[] $payrolls
 * @property Payroll[] $payrolls0
 * @property Position $position
 * @property Schedule[] $schedules
 */
class Account extends BaseModel
{

    public $allowance_items;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['join_date', 'employee_code', 'phone', 'id_department', 'id_position', 'basic_salary'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 1],
            [['is_online'], 'default', 'value' => 0],
            [['id_client', 'id_company', 'name', 'email'], 'required'],
            [['password'], 'required', 'on' => 'create'],
            [['id_client', 'id_company', 'status', 'id_department', 'id_position', 'basic_salary', 'is_online', 'hourly_rate'], 'integer'],
            [['email'], 'unique'],
            [['password', 'token'], 'string'],
            [['ptkp'], 'validatePtkp'],
            [['allowance'], 'default', 'value' => []],
            [['allowance', 'allowance_items', 'created_at', 'updated_at'], 'safe'],
            [['name', 'email', 'join_date', 'employee_code', 'phone'], 'string', 'max' => 255],
            [['id_client'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['id_client' => 'id_client']],
            [['id_company'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['id_company' => 'id_company']],
            [['id_department'], 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['id_department' => 'id_department']],
            [['id_position'], 'exist', 'skipOnError' => true, 'targetClass' => Position::class, 'targetAttribute' => ['id_position' => 'id_position']],
        ];
    }

    public function validatePtkp($attribute, $params)
    {
        if ($this->$attribute !== null) {
            $validValues = array_keys(self::listPtkp());
            if (!in_array($this->$attribute, $validValues)) {
                $this->addError($attribute, 'Kode status PTKP harus sesuai dengan kode berikut: ' . implode(', ', $validValues));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_user' => 'Id User',
            'id_client' => 'Client',
            'id_company' => 'Perusahaan',
            'name' => 'Nama',
            'email' => 'Email',
            'password' => 'Password',
            'status' => 'Status',
            'join_date' => 'Tanggal Bergabung',
            'employee_code' => 'Kode Karyawan',
            'phone' => 'No. Telepon',
            'id_department' => 'Departemen',
            'id_position' => 'Jabatan',
            'basic_salary' => 'Gaji Pokok',
            'is_online' => 'Online',
            'ptkp' => 'Status PTKP',
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
     * Gets query for [[Company]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::class, ['id_company' => 'id_company']);
    }

    /**
     * Gets query for [[Department]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDepartment()
    {
        return $this->hasOne(Department::class, ['id_department' => 'id_department']);
    }

    /**
     * Gets query for [[LeaveRequests]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLeaveRequests()
    {
        return $this->hasMany(LeaveRequest::class, ['id_user' => 'id_user']);
    }

    /**
     * Gets query for [[LeaveRequests0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLeaveRequests0()
    {
        return $this->hasMany(LeaveRequest::class, ['id_approver' => 'id_user']);
    }

    /**
     * Gets query for [[Logs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLogs()
    {
        return $this->hasMany(Log::class, ['id_user' => 'id_user']);
    }

    /**
     * Gets query for [[Payrolls]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPayrolls()
    {
        return $this->hasMany(Payroll::class, ['id_user' => 'id_user']);
    }

    /**
     * Gets query for [[Payrolls0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPayrolls0()
    {
        return $this->hasMany(Payroll::class, ['id_approver' => 'id_user']);
    }

    /**
     * Gets query for [[Position]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPosition()
    {
        return $this->hasOne(Position::class, ['id_position' => 'id_position']);
    }

    /**
     * Gets query for [[Schedules]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSchedules()
    {
        return $this->hasMany(Schedule::class, ['id_user' => 'id_user']);
    }

    public function getRole()
    {
        return $this->hasOne(AuthAssignment::class, ['user_id' => 'id_user']);
    }

    public static function isUserSubmit($model)
    {
        return GeneralHelper::identity()->id_user == $model->id_user;
    }

    public static function getList()
    {
        $models = self::getQueryByCompany('user')
            ->all();

        return ArrayHelper::map($models, 'id_user', 'name');
    }

    public static function listPtkp($pairKey = false)
    {
        $kategoriPtkp = [
            'TK' => 'A',
            'TK/1' => 'A',
            'K/0' => 'A',
            'TK/2' => 'B',
            'TK/3' => 'B',
            'K/1' => 'B',
            'K/2' => 'B',
            'K/3' => 'C',
        ];

        if ($pairKey) {
            $kategoriPtkp = array_keys($kategoriPtkp);
            $kategoriPtkp = array_combine($kategoriPtkp, $kategoriPtkp);
        }

        return $kategoriPtkp;
    }

}