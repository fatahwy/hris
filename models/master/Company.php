<?php

namespace app\models\master;

use app\helpers\GeneralHelper;
use app\helpers\RoleHelper;
use Yii;
use yii\helpers\ArrayHelper;
use app\models\BaseModel;
use app\models\trx\Payroll;
use app\models\trx\Schedule;
use app\models\master\Paket;
use app\models\trx\Subscription;
use app\models\master\Account;

/**
 * This is the model class for table "company".
 *
 * @property int $id_company
 * @property int $id_client
 * @property string $name
 * @property string|null $address
 * @property int $status
 * @property int $max_user
 * @property int $payroll_cutoff_date
 * @property string|null $expired_at
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
 * @property \app\models\trx\Subscription[] $subscriptions
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
            [['payroll_cutoff_date'], 'default', 'value' => 20],
            [['id_client', 'name'], 'required'],
            [['id_client', 'status', 'max_user', 'payroll_cutoff_date'], 'integer'],
            [['payroll_cutoff_date'], 'integer', 'min' => 1, 'max' => 28],
            [['allowance', 'setting'], 'default', 'value' => []],
            [['allowance', 'setting', 'expired_at', 'created_at', 'updated_at'], 'safe'],
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
            'payroll_cutoff_date' => 'Tanggal Cut Off Payroll',
            'expired_at' => 'Tgl Kadaluarsa',
            'allowance' => 'Tunjangan',
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

    /**
     * Gets query for [[Subscriptions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriptions()
    {
        return $this->hasMany(\app\models\trx\Subscription::class, ['id_company' => 'id_company'])->orderBy(['id_subscription' => SORT_DESC]);
    }

    /**
     * Mendapatkan subscription aktif atau subscription terakhir dari company
     * @return \app\models\trx\Subscription|null
     */
    public function getActiveSubscription()
    {
        $active = $this->getSubscriptions()
            ->andWhere(['status' => Subscription::STATUS_ACTIVE])
            ->one();

        if ($active) {
            return $active;
        }

        return $this->getSubscriptions()->one();
    }

    /**
     * Mendapatkan paket subscription company
     * @return \app\models\master\Paket|null
     */
    public function getSubscriptionPaket()
    {
        $subscription = $this->getActiveSubscription();
        return $subscription ? $subscription->paket : null;
    }

    /**
     * Mendapatkan batas maksimal user sesuai paket subscription company.
     * Jika ditemukan paket subscription, nilai max_user pada company disinkronkan.
     * @return int
     */
    public function getMaxUser()
    {
        $paket = $this->getSubscriptionPaket();
        if ($paket && !empty($paket->max_user)) {
            $max = (int) $paket->max_user;
            if ($this->max_user !== $max) {
                $this->max_user = $max;
                $this->updateAttributes(['max_user' => $max]);
            }
            return $max;
        }

        return (int) ($this->max_user ?: 10);
    }

    /**
     * Menghitung jumlah user yang sudah dibuat untuk company ini (tidak termasuk super admin)
     * @return int
     */
    public function getUserCount()
    {
        return (int) Account::find()
            ->alias('u')
            ->leftJoin('auth_assignment a', 'u.id_user = a.user_id')
            ->where(['u.id_company' => $this->id_company])
            ->andWhere(['or', ['!=', 'LOWER(a.item_name)', 'super'], ['a.item_name' => null]])
            ->count();
    }

    /**
     * Cek apakah company masih bisa menambah user baru (belum mencapai max_user)
     * @return bool
     */
    public function canAddUser()
    {
        return $this->getUserCount() < $this->getMaxUser();
    }

    /**
     * Cek apakah masa berlaku / expired_at company sudah berakhir/melebihi tanggal saat ini
     * @return bool
     */
    public function isExpired()
    {
        if (empty($this->expired_at)) {
            return false;
        }

        return strtotime($this->expired_at) < time();
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

    public function getSetting()
    {
        $row = (new \yii\db\Query())
            ->from('company')
            ->where(['id_company' => $this->id_company])
            ->one();

        if ($row && !empty($row['setting'])) {
            return is_string($row['setting']) ? json_decode($row['setting'], true) : $row['setting'];
        }
        return [];
    }

    public function saveSetting($data)
    {
        $company = Company::findOne($this->id_company);
        if ($company) {
            $company->setting = $data;
            $company->save(false);
        }

        return true;
    }

    /**
     * Menghitung rentang tanggal periode payroll berdasarkan tanggal cut off.
     * Alur: Ketika diatur tanggal cut off (default: 20), maka payroll dihitung
     * dari tanggal setelah tanggal cut off bulan sebelumnya (contoh: tanggal 21 bulan lalu)
     * sampai tanggal cut off bulan filter/berjalan (contoh: tanggal 20 bulan ini).
     *
     * @param int|null $cutoffDay Tanggal cut off (1 - 28), default 20
     * @param int|string|null $month Bulan filter (1 - 12)
     * @param int|string|null $year Tahun filter (contoh: 2026)
     * @return array ['start' => 'YYYY-MM-DD', 'end' => 'YYYY-MM-DD']
     */
    public static function calculatePayrollPeriod($cutoffDay = 20, $month = null, $year = null)
    {
        $month = (int) ($month ?: date('n'));
        $year = (int) ($year ?: date('Y'));
        $cutoff = (int) ($cutoffDay ?: 20);
        if ($cutoff < 1) {
            $cutoff = 1;
        } else if ($cutoff > 28) {
            $cutoff = 28;
        }

        // Bulan sebelumnya
        if ($month === 1) {
            $prevMonth = 12;
            $prevYear = $year - 1;
        } else {
            $prevMonth = $month - 1;
            $prevYear = $year;
        }

        // Tanggal cut off bulan sebelumnya
        $daysInPrevMonth = (int) date('t', strtotime(sprintf('%04d-%02d-01', $prevYear, $prevMonth)));
        $prevCutoffDay = min($cutoff, $daysInPrevMonth);
        $prevCutoffDate = sprintf('%04d-%02d-%02d', $prevYear, $prevMonth, $prevCutoffDay);
        // Periode awal adalah 1 hari setelah cut off bulan sebelumnya
        $periodStart = date('Y-m-d', strtotime("$prevCutoffDate +1 day"));

        // Tanggal cut off bulan filter/berjalan
        $daysInCurMonth = (int) date('t', strtotime(sprintf('%04d-%02d-01', $year, $month)));
        $curCutoffDay = min($cutoff, $daysInCurMonth);
        $periodEnd = sprintf('%04d-%02d-%02d', $year, $month, $curCutoffDay);

        return [
            'start' => $periodStart,
            'end' => $periodEnd,
        ];
    }

    /**
     * Mendapatkan periode tanggal payroll untuk company ini.
     *
     * @param int|string|null $month
     * @param int|string|null $year
     * @return array ['start' => 'YYYY-MM-DD', 'end' => 'YYYY-MM-DD']
     */
    public function getPayrollPeriod($month = null, $year = null)
    {
        return self::calculatePayrollPeriod($this->payroll_cutoff_date ?: 20, $month, $year);
    }

}