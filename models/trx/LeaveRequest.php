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
 * @property int|null $id_schedule
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
 * @property Schedule|null $schedule
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
            [['attachment', 'id_schedule'], 'default', 'value' => null],
            [['id_user', 'id_leave_type', 'start_date', 'end_date', 'total_day', 'reason', 'status'], 'required'],
            [
                ['id_schedule'],
                'required',
                'when' => function ($model) {
                    return in_array((int) $model->id_leave_type, LeaveType::P_ON_DUTY);
                },
                'whenClient' => "function (attribute, value) {
                var scheduleTypes = [" . implode(',', LeaveType::P_ON_DUTY) . "];
                var leaveTypeId = $('#leaverequest-id_leave_type').val();
                return scheduleTypes.indexOf(parseInt(leaveTypeId)) !== -1;
            }",
                'message' => 'Jadwal Kerja harus dipilih untuk jenis izin ini.'
            ],
            [['id_approver', 'approve_reason'], 'required', 'on' => 'approval'],
            [['id_user', 'id_leave_type', 'id_schedule', 'total_day', 'id_approver'], 'integer'],
            [['start_date', 'end_date', 'approve_at', 'created_at', 'updated_at'], 'safe'],
            [['reason', 'status', 'approve_reason'], 'string'],
            [['attachment'], 'safe'],
            ['status', 'in', 'range' => array_keys(self::optsStatus())],
            [['id_leave_type'], 'exist', 'skipOnError' => true, 'targetClass' => LeaveType::class, 'targetAttribute' => ['id_leave_type' => 'id_leave_type']],
            [['id_schedule'], 'exist', 'skipOnError' => true, 'targetClass' => Schedule::class, 'targetAttribute' => ['id_schedule' => 'id_schedule']],
            [['id_user'], 'exist', 'skipOnError' => true, 'targetClass' => Account::class, 'targetAttribute' => ['id_user' => 'id_user']],
            [['id_approver'], 'exist', 'skipOnError' => true, 'targetClass' => Account::class, 'targetAttribute' => ['id_approver' => 'id_user']],
            [
                'end_date',
                'compare',
                'compareAttribute' => 'start_date',
                'operator' => '>=',
                'enableClientValidation' => false,
                'message' => 'Tanggal Selesai harus setelah atau sama dengan Tanggal Mulai.'
            ],
            ['total_day', 'validateMaxDay'],
        ];
    }

    public function beforeValidate()
    {
        if ($this->isNewRecord) {
            $this->status = self::STATUS_PENDING;
        }

        $leaveType = $this->leaveType;
        if ($leaveType && $leaveType->isCategoryLeave()) {
            if ($this->start_date && $this->end_date) {
                $this->total_day = GeneralHelper::countDay($this->start_date, $this->end_date);
            } else {
                $this->total_day = 0;
            }
        }

        return parent::beforeValidate();
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord && empty($this->status)) {
            $this->status = self::STATUS_PENDING;
        }

        $leaveType = $this->leaveType;
        if ($leaveType && $leaveType->isCategoryLeave()) {
            $this->total_day = GeneralHelper::countDay($this->start_date, $this->end_date);
        }

        return parent::beforeSave($insert);
    }

    /**
     * Custom validation to check max_day or user remaining_leave
     */
    public function validateMaxDay($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $leaveType = $this->leaveType;
            if ($leaveType && $leaveType->isCategoryLeave()) {
                $user = Account::findOne($this->id_user);
                if (!$user) {
                    return;
                }

                // Khusus cuti tahunan (L_YEARLY)
                if ((int) $this->id_leave_type === LeaveType::L_YEARLY) {
                    $today = date('Y-m-d');

                    // 1. Pastikan user.leave_entitlement_date sudah aktif (sekarang >= leave_entitlement_date)
                    if (empty($user->leave_entitlement_date)) {
                        $this->addError('id_leave_type', 'Pengajuan cuti tahunan gagal. Anda belum memiliki Mulai Hak Cuti.');
                        return;
                    }

                    if ($today < $user->leave_entitlement_date) {
                        $formattedDate = Yii::$app->formatter->asDate($user->leave_entitlement_date, 'php:d F Y');
                        $this->addError('id_leave_type', 'Pengajuan cuti tahunan belum dapat dilakukan. Hak cuti tahunan Anda baru mulai aktif pada tanggal ' . $formattedDate . '.');
                        return;
                    }

                    // 2. Kuota diambil dari user.remaining_leave
                    $remaining = self::getRemainingDays($user, $this->id_leave_type, $this->start_date ?: $today, $this->id_leave_request);
                    if ($remaining <= 0) {
                        $this->addError($attribute, 'Pengajuan cuti tahunan gagal. Sisa kuota cuti tahunan Anda sudah habis (0 hari).');
                        return;
                    }

                    if ($this->$attribute > $remaining) {
                        $this->addError($attribute, 'Total hari cuti (' . $this->$attribute . ' hari) melebihi sisa cuti tahunan Anda yang tersedia (' . $remaining . ' hari).');
                        return;
                    }
                } elseif ($leaveType->max_day !== null) {
                    $remaining = self::getRemainingDays($user, $this->id_leave_type, $this->start_date ?: date('Y-m-d'), $this->id_leave_request);
                    if ($this->$attribute > $remaining) {
                        $this->addError($attribute, 'Total hari cuti melebihi kuota (' . $leaveType->max_day . ' hari). Sisa cuti Anda (' . $remaining . ' hari).');
                    }
                }
            }
        }
    }

    /**
     * Get the remaining leave days for a user and leave type at a given reference date.
     *
     * @param Account|int $user
     * @param int $id_leave_type
     * @param string|null $reference_date
     * @param int|null $exclude_id
     * @return int|null
     */
    public static function getRemainingDays($user, $id_leave_type, $reference_date = null, $exclude_id = null)
    {
        if ($reference_date === null) {
            $reference_date = date('Y-m-d');
        }

        if (!$user instanceof Account) {
            $user = Account::findOne($user);
        }
        if (!$user) {
            return null;
        }

        $leaveType = LeaveType::find()
            ->where(['id_leave_type' => $id_leave_type])
            ->andWhere(['category' => LeaveType::CATEGORY_LEAVE])
            ->cache(10)
            ->one();
        if (!$leaveType) {
            return null;
        }

        // Khusus cuti tahunan (L_YEARLY), kuota diambil dari user.remaining_leave
        if ((int) $id_leave_type === LeaveType::L_YEARLY) {
            $baseRemaining = (int) ($user->remaining_leave ?? 0);
            if ($exclude_id !== null) {
                $existing = self::findOne($exclude_id);
                if ($existing && (int) $existing->id_leave_type === LeaveType::L_YEARLY && $existing->status !== self::STATUS_REJECT) {
                    $baseRemaining += (int) $existing->total_day;
                }
            }
            return max(0, $baseRemaining);
        }

        if ($leaveType->max_day === null) {
            return null;
        }

        $id_user = $user->id_user;
        if (!$user->join_date) {
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

        $refYear = (int) $refDateTime->format('Y');
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
            $newYear = (int) $periodStart->format('Y');
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
        $leaveType = $this->leaveType;
        $flag = in_array((int) $this->id_leave_type, LeaveType::P_ON_DUTY);
        return [
            'id_leave_request' => 'Id',
            'id_user' => 'Pegawai',
            'id_leave_type' => 'Tipe ' . ($leaveType && $leaveType->isCategoryPermission() ? 'Izin' : 'Cuti'),
            'id_schedule' => 'Jadwal Kerja',
            'start_date' => $flag ? 'Jam Mulai' : 'Tanggal Mulai',
            'end_date' => $flag ? 'Jam Selesai' : 'Tanggal Selesai',
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
     * Gets query for [[Schedule]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSchedule()
    {
        return $this->hasOne(Schedule::class, ['id_schedule' => 'id_schedule']);
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

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ((int) $this->id_leave_type === LeaveType::L_YEARLY) {
            if ($insert) {
                // Pemotongan kuota ketika mengajukan cuti
                if ($this->status !== self::STATUS_REJECT) {
                    Account::updateAllCounters(['remaining_leave' => -$this->total_day], ['id_user' => $this->id_user]);
                }
            } else {
                $statusChanged = array_key_exists('status', $changedAttributes);
                $totalDayChanged = array_key_exists('total_day', $changedAttributes);
                $oldStatus = $statusChanged ? $changedAttributes['status'] : $this->status;
                $newStatus = $this->status;
                $oldTotalDay = $totalDayChanged ? (int) $changedAttributes['total_day'] : $this->total_day;
                $newTotalDay = (int) $this->total_day;

                // 1. Pengembalian kuota ketika status reject / pemotongan kembali jika batal reject
                if ($statusChanged) {
                    if ($oldStatus !== self::STATUS_REJECT && $newStatus === self::STATUS_REJECT) {
                        Account::updateAllCounters(['remaining_leave' => $oldTotalDay], ['id_user' => $this->id_user]);
                    } elseif ($oldStatus === self::STATUS_REJECT && $newStatus !== self::STATUS_REJECT) {
                        Account::updateAllCounters(['remaining_leave' => -$newTotalDay], ['id_user' => $this->id_user]);
                    }
                }

                // 2. Perubahan durasi hari (total_day) pada permohonan aktif
                if ($totalDayChanged && $newStatus !== self::STATUS_REJECT && (!$statusChanged || $oldStatus !== self::STATUS_REJECT)) {
                    $diff = $newTotalDay - $oldTotalDay;
                    if ($diff !== 0) {
                        Account::updateAllCounters(['remaining_leave' => -$diff], ['id_user' => $this->id_user]);
                    }
                }
            }
        }
    }

    public function afterDelete()
    {
        parent::afterDelete();
        // Pengembalian kuota ketika dihapus (kecuali jika sudah berstatus REJECT karena sudah dikembalikan saat reject)
        if ((int) $this->id_leave_type === LeaveType::L_YEARLY && $this->status !== self::STATUS_REJECT) {
            Account::updateAllCounters(['remaining_leave' => $this->total_day], ['id_user' => $this->id_user]);
        }
    }
}