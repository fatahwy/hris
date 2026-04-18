<?php

namespace app\models\trx;

use app\helpers\GeneralHelper;
use app\models\BaseModel;
use app\models\master\Account;
use app\models\master\LeaveType;
use Yii;

/**
 * This is the model class for table "leave_request".
 *
 * @property int $id_leave_request
 * @property int $id_user
 * @property int $id_leave_type
 * @property string $start_date
 * @property string $end_date
 * @property int $total_day
 * @property string $reason
 * @property string|null $attachment
 * @property string $status
 * @property int $id_approver
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Account $approver
 * @property LeaveType $leaveType
 * @property Account $user
 */
class LeaveRequest extends BaseModel
{

    /**
     * ENUM field values
     */
    const STATUS_PENDING = 'PENDING';
    const STATUS_REJECT = 'REJECT';
    const STATUS_APPROVE = 'APPROVE';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'leave_request';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['attachment'], 'default', 'value' => null],
            [['id_user', 'id_leave_type', 'start_date', 'end_date', 'total_day', 'reason', 'status'], 'required'],
            [['id_approver', 'approve_reason'], 'required', 'on' => 'approval'],
            [['id_user', 'id_leave_type', 'total_day', 'id_approver'], 'integer'],
            [['start_date', 'end_date', 'approve_at', 'created_at', 'updated_at'], 'safe'],
            [['reason', 'attachment', 'status', 'approve_reason'], 'string'],
            ['status', 'in', 'range' => array_keys(self::optsStatus())],
            [['id_leave_type'], 'exist', 'skipOnError' => true, 'targetClass' => LeaveType::class, 'targetAttribute' => ['id_leave_type' => 'id_leave_type']],
            [['id_user'], 'exist', 'skipOnError' => true, 'targetClass' => Account::class, 'targetAttribute' => ['id_user' => 'id_user']],
            [['id_approver'], 'exist', 'skipOnError' => true, 'targetClass' => Account::class, 'targetAttribute' => ['id_approver' => 'id_user']],
            ['total_day', 'validateMaxDay'],
        ];
    }

    public function beforeSave($insert)
    {
        $this->total_day = GeneralHelper::countDay($this->start_date, $this->end_date);

        return parent::beforeSave($insert);
    }

    /**
     * Custom validation to check max_day of selected LeaveType
     */
    public function validateMaxDay($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $leaveType = $this->leaveType;
            if ($leaveType && $leaveType->max_day !== null) {
                // Calculate used days (approved or pending)
                $usedDays = self::find()
                    ->where(['id_user' => $this->id_user, 'id_leave_type' => $this->id_leave_type])
                    ->andWhere(['!=', 'status', self::STATUS_REJECT])
                    ->andFilterWhere(['!=', 'id_leave_request', $this->id_leave_request])
                    ->sum('total_day') ?: 0;

                if (($usedDays + $this->$attribute) > $leaveType->max_day) {
                    $remaining = max(0, $leaveType->max_day - $usedDays);
                    $this->addError($attribute, 'Total leave days exceed the maximum allowed (' . $leaveType->max_day . ' days). You only have ' . $remaining . ' days left.');
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_leave_request' => 'Id Leave Request',
            'id_user' => 'Id User',
            'id_leave_type' => 'Jenis Cuti',
            'start_date' => 'Tanggal Mulai',
            'end_date' => 'Tanggal Selesai',
            'total_day' => 'Total Hari',
            'reason' => 'Alasan',
            'attachment' => 'Attachment',
            'status' => 'Status',
            'id_approver' => 'Disetujui Oleh',
            'created_at' => 'Tgl Buat',
            'updated_at' => 'Tgl Update',
        ];
    }

    /**
     * Gets query for [[Approver]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getApprover()
    {
        return $this->hasOne(Account::class, ['id_user' => 'id_approver']);
    }

    /**
     * Gets query for [[LeaveType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLeaveType()
    {
        return $this->hasOne(LeaveType::class, ['id_leave_type' => 'id_leave_type']);
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
            self::STATUS_REJECT => 'REJECT',
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
    public function isStatusPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function setStatusToPending()
    {
        $this->status = self::STATUS_PENDING;
    }

    /**
     * @return bool
     */
    public function isStatusReject()
    {
        return $this->status === self::STATUS_REJECT;
    }

    public function setStatusToReject()
    {
        $this->status = self::STATUS_REJECT;
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