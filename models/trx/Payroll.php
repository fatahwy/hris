<?php

namespace app\models\trx;

use app\models\master\Account;
use app\models\master\Company;
use app\models\BaseModel;
use app\models\User;
use Yii;

/**
 * This is the model class for table "payroll".
 *
 * @property int $id_payroll
 * @property int $id_company
 * @property int $id_user
 * @property string $period_start
 * @property string $period_end
 * @property int $basic_salary
 * @property int $allowance
 * @property int $overtime
 * @property int $dedection
 * @property int $tax
 * @property int $net_salary
 * @property string $status
 * @property int $id_user_generate
 * @property int $id_user_verify
 * @property int $id_user_approve
 * @property string|null $user_verify_at
 * @property string|null $user_approve_at
 * @property string|null $created_at
 * @property string|null $updated_at
 *1
 * @property Account $approver
 * @property Company $company
 * @property Account $user
 */
class Payroll extends BaseModel
{

    /**
     * ENUM field values
     */
    const STATUS_PENDING = 'PENDING';
    const STATUS_DRAFT = 'DRAFT';
    const STATUS_APPROVE = 'APPROVE';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payroll';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['net_salary'], 'default', 'value' => 0],
            [['id_company', 'id_user', 'period_start', 'period_end', 'status'], 'required'],
            [['id_company', 'id_user', 'basic_salary', 'allowance', 'overtime', 'dedection', 'tax', 'net_salary', 'id_user_generate', 'id_user_verify', 'id_user_approve'], 'integer'],
            [['period_start', 'period_end', 'user_verify_at', 'user_approve_at', 'created_at', 'updated_at'], 'safe'],
            [['status'], 'string'],
            ['status', 'in', 'range' => array_keys(self::optsStatus())],
            [['id_company'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['id_company' => 'id_company']],
            [['id_user'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['id_user' => 'id_user']],
            [['id_user_generate'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['id_user_generate' => 'id_user']],
            [['id_user_verify'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['id_user_verify' => 'id_user']],
            [['id_user_approve'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['id_user_approve' => 'id_user']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_payroll' => 'Id Payroll',
            'id_company' => 'Id Company',
            'id_user' => 'Id User',
            'period_start' => 'Period Start',
            'period_end' => 'Period End',
            'basic_salary' => 'Basic Salary',
            'allowance' => 'Allowance',
            'overtime' => 'Overtime',
            'dedection' => 'Dedection',
            'tax' => 'Tax',
            'net_salary' => 'Net Salary',
            'status' => 'Status',
            'id_user_approve' => 'User Approval',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Approver]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getApprover()
    {
        return $this->hasOne(Account::class, ['id_user' => 'id_user_approve']);
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
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Account::class, ['id_user' => 'id_user']);
    }


    /**
     * column status ENUM value labels
     * @return string[]
     */
    public static function optsStatus()
    {
        return [
            self::STATUS_PENDING => 'PENDING',
            self::STATUS_DRAFT => 'DRAFT',
            self::STATUS_APPROVE => 'APPROVE',
        ];
    }

    /**
     * @return string
     */
    public function displayStatus()
    {
        return self::optsStatus()[$this->status];
    }

    /**
     * @return bool
     */
    public function isStatusDraft()
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function setStatusToDraft()
    {
        $this->status = self::STATUS_DRAFT;
    }

    /**
     * @return bool
     */
    public function isStatusApprove()
    {
        return $this->status === self::STATUS_APPROVE;
    }

    public function setStatusToApprove()
    {
        $this->status = self::STATUS_APPROVE;
    }
}