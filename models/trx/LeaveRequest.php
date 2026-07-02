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
        if ($insert) {
            $this->status = self::STATUS_PENDING;
        }
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
                $remaining = self::getRemainingDays($this->id_user, $this->id_leave_type, $this->start_date ?: date('Y-m-d'), $this->id_leave_request);
                if ($this->$attribute > $remaining) {
                    $this->addError($attribute, 'Total leave days exceed the maximum allowed (' . $leaveType->max_day . ' days). You only have ' . $remaining . ' days left.');
                }
            }
        }
    }

    /**
     * Get the remaining leave days for a user and leave type at a given reference date.
     *
     * @param int $id_user
     * @param int $id_leave_type
     * @param string|null $reference_date
     * @param int|null $exclude_id
     * @return int|null
     */
    public static function getRemainingDays($id_user, $id_leave_type, $reference_date = null, $exclude_id = null)
    {
        if ($reference_date === null) {
            $reference_date = date('Y-m-d');
        }

        $leaveType = LeaveType::findOne($id_leave_type);
        if (!$leaveType || $leaveType->max_day === null) {
            return null;
        }

        $user = Account::findOne($id_user);
        if (!$user || !$user->join_date) {
            $query = self::find()
                ->where(['id_user' => $id_user, 'id_leave_type' => $id_leave_type])
                ->andWhere(['!=', 'status', self::STATUS_REJECT]);
            if ($exclude_id !== null) {
                $query->andWhere(['!=', 'id_leave_request', $exclude_id]);
            }
            $usedDays = $query->sum('total_day') ?: 0;
            return max(0, $leaveType->max_day - $usedDays);
        }

        $period = self::getLeavePeriod($user->join_date, $reference_date);
        if (!$period) {
            return $leaveType->max_day;
        }

        $query = self::find()
            ->where(['id_user' => $id_user, 'id_leave_type' => $id_leave_type])
            ->andWhere(['!=', 'status', self::STATUS_REJECT])
            ->andWhere(['between', 'start_date', $period['start'], $period['end']]);
        if ($exclude_id !== null) {
            $query->andWhere(['!=', 'id_leave_request', $exclude_id]);
        }
        $usedDays = $query->sum('total_day') ?: 0;

        return max(0, $leaveType->max_day - $usedDays);
    }

    /**
     * Get the leave period start and end date based on join date and reference date.
     *
     * @param string $joinDate
     * @param string $referenceDate
     * @return array|null
     */
    public static function getLeavePeriod($joinDate, $referenceDate)
    {
        if (!$joinDate) {
            return null;
        }

        try {
            $joinDateTime = new \DateTime($joinDate);
            $refDateTime = new \DateTime($referenceDate);
        } catch (\Exception $e) {
            return null;
        }

        $refYear = (int)$refDateTime->format('Y');
        $joinMonthDay = $joinDateTime->format('m-d');

        if ($joinMonthDay === '02-29') {
            $isLeap = ((($refYear % 4) == 0) && ((($refYear % 100) != 0) || (($refYear % 400) == 0)));
            if (!$isLeap) {
                $joinMonthDay = '02-28';
            }
        }

        try {
            $periodStart = new \DateTime(sprintf('%d-%s', $refYear, $joinMonthDay));
        } catch (\Exception $e) {
            return null;
        }

        if ($refDateTime < $periodStart) {
            $periodStart->modify('-1 year');
            $newYear = (int)$periodStart->format('Y');
            if ($joinDateTime->format('m-d') === '02-29') {
                $isLeap = ((($newYear % 4) == 0) && ((($newYear % 100) != 0) || (($newYear % 400) == 0)));
                $periodStart = new \DateTime(sprintf('%d-%s', $newYear, $isLeap ? '02-29' : '02-28'));
            }
        }

        $periodEnd = clone $periodStart;
        $periodEnd->modify('+1 year -1 day');

        return [
            'start' => $periodStart->format('Y-m-d'),
            'end' => $periodEnd->format('Y-m-d')
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_leave_request' => 'Id',
            'id_user' => 'Pegawai',
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