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
 * @property string|null $allowance
 * @property int $overtime
 * @property int $dedection
 * @property int $tax
 * @property int $gross_salary
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
            [['id_company', 'id_user', 'basic_salary', 'overtime', 'dedection', 'tax', 'gross_salary', 'net_salary', 'id_user_generate', 'id_user_verify', 'id_user_approve'], 'integer'],
            [['period_start', 'period_end', 'user_verify_at', 'user_approve_at', 'created_at', 'updated_at'], 'safe'],
            [['status'], 'string'],
            [['allowance'], 'safe'],
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
            'id_company' => 'Perusahaan',
            'id_user' => 'Karyawan',
            'period_start' => 'Periode Awal',
            'period_end' => 'Periode Akhir',
            'basic_salary' => 'Gaji Pokok',
            'allowance' => 'Tunjangan',
            'overtime' => 'Lembur',
            'dedection' => 'Potongan',
            'tax' => 'Pajak',
            'gross_salary' => 'Gaji Kotor',
            'net_salary' => 'Gaji Bersih',
            'status' => 'Status',
            'id_user_approve' => 'User Approval',
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

    public static function getTER(?string $categoryTer, float|int $penghasilan): float
    {
        if(!$categoryTer) {
            return 0;
        }

        $tarif = [];
        if ($categoryTer === 'A') {
            $tarif = [
                1400000000 => 0.34,
                910000000 => 0.33,
                695000000 => 0.32,
                550000000 => 0.31,
                454000000 => 0.30,
                337000000 => 0.29,
                206000000 => 0.28,
                157000000 => 0.27,
                125000000 => 0.26,
                103000000 => 0.25,
                89000000 => 0.24,
                77500000 => 0.23,
                68600000 => 0.22,
                62200000 => 0.21,
                56300000 => 0.20,
                51400000 => 0.19,
                47800000 => 0.18,
                43850000 => 0.17,
                39100000 => 0.16,
                35400000 => 0.15,
                32400000 => 0.14,
                30050000 => 0.13,
                28000000 => 0.12,
                26450000 => 0.11,
                24150000 => 0.10,
                19750000 => 0.09,
                16950000 => 0.08,
                15100000 => 0.07,
                13750000 => 0.06,
                12500000 => 0.05,
                11600000 => 0.04,
                11050000 => 0.035,
                10700000 => 0.03,
                10350000 => 0.025,
                10050000 => 0.0225,
                9650000 => 0.02,
                8550000 => 0.0175,
                7500000 => 0.015,
                6750000 => 0.0125,
                6300000 => 0.01,
                5950000 => 0.0075,
                5650000 => 0.005,
                5400000 => 0.0025,
            ];
        } else if ($categoryTer == 'B') {
            $tarif = [
                1405000000 => 0.34,
                957000000 => 0.33,
                704000000 => 0.32,
                555000000 => 0.31,
                459000000 => 0.30,
                374000000 => 0.29,
                211000000 => 0.28,
                163000000 => 0.27,
                129000000 => 0.26,
                109000000 => 0.25,
                93000000 => 0.24,
                80000000 => 0.23,
                71000000 => 0.22,
                64000000 => 0.21,
                58500000 => 0.20,
                53800000 => 0.19,
                49500000 => 0.18,
                45800000 => 0.17,
                41100000 => 0.16,
                37100000 => 0.15,
                33950000 => 0.14,
                31450000 => 0.13,
                29350000 => 0.12,
                27700000 => 0.11,
                26000000 => 0.10,
                21850000 => 0.09,
                18450000 => 0.08,
                16400000 => 0.07,
                14950000 => 0.06,
                13600000 => 0.05,
                12600000 => 0.04,
                11600000 => 0.03,
                11250000 => 0.025,
                10750000 => 0.02,
                9200000 => 0.015,
                7300000 => 0.01,
                6850000 => 0.0075,
                6500000 => 0.005,
                6200000 => 0.0025,
            ];
        } else if ($categoryTer == 'C') {
            $tarif = [
                1419000000 => 0.34,
                965000000 => 0.33,
                709000000 => 0.32,
                561000000 => 0.31,
                463000000 => 0.30,
                390000000 => 0.29,
                221000000 => 0.28,
                169000000 => 0.27,
                134000000 => 0.26,
                110000000 => 0.25,
                95600000 => 0.24,
                83200000 => 0.23,
                74500000 => 0.22,
                66700000 => 0.21,
                60400000 => 0.20,
                55800000 => 0.19,
                51200000 => 0.18,
                47400000 => 0.17,
                43000000 => 0.16,
                38900000 => 0.15,
                35400000 => 0.14,
                32600000 => 0.13,
                30100000 => 0.12,
                28100000 => 0.11,
                26600000 => 0.10,
                22700000 => 0.09,
                19500000 => 0.08,
                17050000 => 0.07,
                15550000 => 0.06,
                14150000 => 0.05,
                12950000 => 0.04,
                12050000 => 0.03,
                11200000 => 0.02,
                10950000 => 0.0175,
                9800000 => 0.015,
                8850000 => 0.0125,
                7800000 => 0.01,
                7350000 => 0.0075,
                6950000 => 0.005,
                6600000 => 0.0025,
            ];
        }

        foreach ($tarif as $batas => $persen) {
            if ($penghasilan > $batas) {
                return $persen;
            }
        }

        return 0;
    }

}