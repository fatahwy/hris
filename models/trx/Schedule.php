<?php

namespace app\models\trx;

use app\helpers\DBHelper;
use app\models\master\Account;
use app\models\master\Company;
use app\models\master\Shift;
use app\models\BaseModel;
use Yii;

/**
 * This is the model class for table "schedule".
 *
 * @property int $id_schedule
 * @property int $id_company
 * @property int $id_user
 * @property int $id_shift
 * @property string $date
 * @property string $shift_name
 * @property string $checkin_start
 * @property string $workhour_start
 * @property string $workhour_end
 * @property string $status
 * @property string|null $checkin_datetime
 * @property string|null $checkin_lat
 * @property string|null $checkin_long
 * @property string|null $checkin_photo
 * @property string|null $checkout_datetime
 * @property string|null $checkout_lat
 * @property string|null $checkout_long
 * @property string|null $checkout_photo
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Company $company
 * @property Shift $shift
 * @property Account $user
 */
class Schedule extends BaseModel
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'schedule';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['checkin_datetime', 'checkin_lat', 'checkin_long', 'checkin_photo', 'checkout_datetime', 'checkout_lat', 'checkout_long', 'checkout_photo'], 'default', 'value' => null],
            [['id_company', 'id_user', 'id_shift', 'date', 'shift_name', 'checkin_start', 'workhour_start', 'workhour_end', 'status'], 'required'],
            [['id_company', 'id_user', 'id_shift'], 'integer'],
            [['date', 'checkin_start', 'workhour_start', 'workhour_end', 'checkin_datetime', 'checkout_datetime', 'created_at', 'updated_at'], 'safe'],
            [['shift_name', 'checkin_lat', 'checkin_long', 'checkin_photo', 'checkout_lat', 'checkout_long', 'checkout_photo'], 'string', 'max' => 255],
            [['status'], 'string', 'max' => 45],
            [['id_company'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['id_company' => 'id_company']],
            [['id_user'], 'exist', 'skipOnError' => true, 'targetClass' => Account::class, 'targetAttribute' => ['id_user' => 'id_user']],
            [['id_shift'], 'exist', 'skipOnError' => true, 'targetClass' => Shift::class, 'targetAttribute' => ['id_shift' => 'id_shift']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_schedule' => 'Id Schedule',
            'id_company' => 'Perusahaan',
            'id_user' => 'Karyawan',
            'id_shift' => 'Shift',
            'date' => 'Tanggal',
            'shift_name' => 'Nama Shift',
            'checkin_start' => 'Jam Absen Mulai',
            'workhour_start' => 'Jam Kerja Mulai',
            'workhour_end' => 'Jam Kerja Selesai',
            'status' => 'Status',
            'checkin_datetime' => 'Jam Absen Masuk',
            'checkin_lat' => 'Checkin Lat',
            'checkin_long' => 'Checkin Long',
            'checkin_photo' => 'Checkin Photo',
            'checkout_datetime' => 'Jam Absen Pulang',
            'checkout_lat' => 'Checkout Lat',
            'checkout_long' => 'Checkout Long',
            'checkout_photo' => 'Checkout Photo',
            'created_at' => 'Tgl Buat',
            'updated_at' => 'Tgl Update',
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
     * Gets query for [[Shift]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getShift()
    {
        return $this->hasOne(Shift::class, ['id_shift' => 'id_shift']);
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
     * Get the active schedule for the logged in user (checked in, not yet checked out) and more than workhour_end
     * @return Schedule|null
     */
    public static function getActiveScheduleToClockOut()
    {
        $userId = Yii::$app->user->identity->id_user ?? 0;
        return self::find()
            ->where(['id_user' => $userId])
            ->andWhere(['not', ['checkin_datetime' => null]])
            ->andWhere(['checkout_datetime' => null])
            ->andWhere(['<', 'workhour_end', DBHelper::now()])
            ->one();
    }

    /**
     * Get the active schedule for the logged in user (checked in, not yet checked out)
     * @return Schedule|null
     */
    public static function getActiveSchedule()
    {
        $userId = Yii::$app->user->identity->id_user ?? 0;
        return self::find()
            ->where(['id_user' => $userId])
            ->andWhere(['not', ['checkin_datetime' => null]])
            ->andWhere(['checkout_datetime' => null])
            ->one();
    }

    /**
     * Get the available schedule for the logged in user to check in
     * Criteria: date matches, current time is between checkin_start and workhour_end, not yet checked in.
     * Also checks if there is any active schedule that hasn't been checked out.
     * @return Schedule|null
     */
    public static function getAvailableSchedule()
    {
        $userId = Yii::$app->user->identity->id_user ?? 0;

        // If there is an active schedule, don't allow checking into another one
        if (self::getActiveScheduleToClockOut()) {
            return null;
        }

        // Find schedule for today where current time is within window and not checked in
        // Since we need to compare combined dateTime, we might need to be careful with formats
        // but the rules say "antara checkin_start dan workhour_end yang masuk tanggal jam saat ini"

        $schedule = self::find()
            ->where(['id_user' => $userId])
            ->andWhere(['checkin_datetime' => null])
            ->andWhere(['>=', 'checkin_start', DBHelper::now()])
            ->andWhere(['<=', 'workhour_end', DBHelper::now()])
            ->orderBy(['workhour_start' => SORT_ASC])
            ->one();

        return $schedule;

    }

}