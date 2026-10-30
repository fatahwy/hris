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
 * @property string|null $uuid
 * @property array|null $allowance
 * @property string|null $ptkp
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * Properties from Employee Form (references/Form_Karyawan.html):
 * Tab 1: Data Pribadi
 * @property string|null $nik
 * @property string|null $gender
 * @property string|null $birth_place
 * @property string|null $birth_date
 * @property string|null $marital_status
 * @property string|null $religion
 * @property string|null $education
 * @property string|null $photo
 * @property string|null $ktp_address
 * @property int|null $is_domicile_same_ktp
 * @property string|null $domicile_address
 * @property string|null $emergency_contact_name
 * @property string|null $emergency_contact_relation
 * @property string|null $emergency_contact_phone
 *
 * Tab 2: Pekerjaan
 * @property string|null $employment_status
 * @property int|null $id_manager
 * @property string|null $work_location
 * @property string|null $contract_end_date
 * @property string|null $leave_entitlement_date
 * @property string|null $resign_date
 * @property string|null $resign_reason
 * @property int|null $annual_leave_quota
 * @property int|null $remaining_leave
 *
 * Tab 3: Payroll & Pajak
 * @property string|null $npwp
 * @property string|null $tax_method
 * @property string|null $bank_name
 * @property string|null $bank_account_number
 * @property string|null $bank_account_holder
 *
 * Tab 4: BPJS
 * @property string|null $bpjs_kesehatan_number
 * @property string|null $bpjs_ketenagakerjaan_number
 * @property int|null $bpjs_kesehatan
 * @property int|null $bpjs_jht
 * @property int|null $bpjs_jp
 * @property int|null $bpjs_jkk
 * @property int|null $bpjs_jkm
 *
 * Relations:
 * @property Company $client
 * @property Company $company
 * @property Department $department
 * @property Position $position
 * @property Account|null $manager
 * @property LeaveRequest[] $leaveRequests
 * @property LeaveRequest[] $leaveRequests0
 * @property Log[] $logs
 * @property Payroll[] $payrolls
 * @property Payroll[] $payrolls0
 * @property Schedule[] $schedules
 * @property AuthAssignment $role
 */
class Account extends BaseModel
{
    public $allowance_items;
    public $photo_file;

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
            [['join_date', 'employee_code', 'phone', 'id_department', 'id_position', 'basic_salary', 'id_manager', 'contract_end_date', 'leave_entitlement_date', 'resign_date', 'resign_reason'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 1],
            [['is_online'], 'default', 'value' => 0],
            [['annual_leave_quota', 'remaining_leave'], 'default', 'value' => 12],
            [['bpjs_kesehatan', 'bpjs_jht', 'bpjs_jp', 'bpjs_jkk', 'bpjs_jkm', 'is_domicile_same_ktp'], 'default', 'value' => 1],
            [['tax_method'], 'default', 'value' => 'Gross'],
            [['id_client', 'id_company', 'name', 'email'], 'required'],
            [['password'], 'required', 'on' => 'create'],
            [['id_client', 'id_company', 'status', 'id_department', 'id_position', 'id_manager', 'basic_salary', 'is_online', 'hourly_rate', 'annual_leave_quota', 'remaining_leave', 'is_domicile_same_ktp', 'bpjs_kesehatan', 'bpjs_jht', 'bpjs_jp', 'bpjs_jkk', 'bpjs_jkm'], 'integer'],
            [['email'], 'unique'],
            [['id_company'], 'validateMaxUser'],
            [['password', 'token', 'ktp_address', 'domicile_address'], 'string'],
            [['ptkp'], 'validatePtkp'],
            [['allowance'], 'default', 'value' => []],
            [['allowance', 'allowance_items', 'created_at', 'updated_at', 'birth_date', 'contract_end_date', 'leave_entitlement_date', 'resign_date'], 'safe'],
            [['name', 'email', 'join_date', 'employee_code', 'phone', 'photo'], 'string', 'max' => 255],
            [['nik', 'tax_method', 'marital_status', 'religion'], 'string', 'max' => 30],
            [['gender'], 'string', 'max' => 20],
            [['birth_place', 'emergency_contact_name', 'bank_account_holder', 'work_location', 'resign_reason'], 'string', 'max' => 100],
            [['education', 'emergency_contact_relation', 'emergency_contact_phone', 'employment_status', 'npwp', 'bank_name', 'bank_account_number', 'bpjs_kesehatan_number', 'bpjs_ketenagakerjaan_number'], 'string', 'max' => 50],
            [['photo_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, webp', 'maxSize' => 5 * 1024 * 1024],
            [['id_client'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['id_client' => 'id_client']],
            [['id_company'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['id_company' => 'id_company']],
            [['id_department'], 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['id_department' => 'id_department']],
            [['id_position'], 'exist', 'skipOnError' => true, 'targetClass' => Position::class, 'targetAttribute' => ['id_position' => 'id_position']],
            [['id_manager'], 'exist', 'skipOnError' => true, 'targetClass' => Account::class, 'targetAttribute' => ['id_manager' => 'id_user']],
        ];
    }

    public function validatePtkp($attribute, $params)
    {
        if ($this->$attribute !== null && $this->$attribute !== '') {
            $validValues = array_keys(self::listPtkp());
            if (!in_array($this->$attribute, $validValues)) {
                $this->addError($attribute, 'Kode status PTKP harus sesuai dengan kode berikut: ' . implode(', ', $validValues));
            }
        }
    }

    public function validateMaxUser($attribute, $params)
    {
        if ($this->isNewRecord && $this->id_company) {
            $company = Company::findOne($this->id_company);
            if ($company && !$company->canAddUser()) {
                $maxUser = $company->getMaxUser();
                $paket = $company->getSubscriptionPaket();
                $paketText = $paket ? " ({$paket->nama_paket})" : "";
                $this->addError($attribute, "Jumlah user telah mencapai batas maksimal paket subscription{$paketText} yaitu {$maxUser} user.");
                $this->addError('name', "Jumlah user telah mencapai batas maksimal paket subscription{$paketText} ({$maxUser} user). Silakan upgrade paket untuk menambah user baru.");
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_user' => 'ID User',
            'id_client' => 'Client',
            'id_company' => 'Perusahaan',
            'name' => 'Nama Lengkap',
            'email' => 'Email',
            'password' => 'Password',
            'status' => 'Status Akun',
            'join_date' => 'Tanggal Bergabung',
            'employee_code' => 'Kode Karyawan',
            'phone' => 'No. Telepon / WhatsApp',
            'id_department' => 'Departemen',
            'id_position' => 'Jabatan',
            'basic_salary' => 'Gaji Pokok',
            'hourly_rate' => 'Rate Per Jam',
            'is_online' => 'Online',
            'ptkp' => 'Status PTKP',
            'created_at' => 'Tgl Buat',
            'updated_at' => 'Tgl Update',

            // Tab 1: Identitas
            'nik' => 'NIK KTP',
            'gender' => 'Jenis Kelamin',
            'birth_place' => 'Tempat Lahir',
            'birth_date' => 'Tanggal Lahir',
            'marital_status' => 'Status Pernikahan',
            'religion' => 'Agama',
            'education' => 'Pendidikan Terakhir',
            'photo' => 'Foto Profil',
            'photo_file' => 'Upload Foto Profil',

            // Tab 1: Alamat
            'ktp_address' => 'Alamat Sesuai KTP',
            'is_domicile_same_ktp' => 'Domisili Sama dengan KTP',
            'domicile_address' => 'Alamat Domisili',

            // Tab 1: Kontak Darurat
            'emergency_contact_name' => 'Nama Kontak Darurat',
            'emergency_contact_relation' => 'Hubungan Kontak Darurat',
            'emergency_contact_phone' => 'No. Telepon Kontak Darurat',

            // Tab 2: Pekerjaan
            'employment_status' => 'Status Kepegawaian',
            'id_manager' => 'Atasan Langsung',
            'work_location' => 'Lokasi / Cabang Kerja',
            'contract_end_date' => 'Tanggal Akhir Kontrak',
            'leave_entitlement_date' => 'Mulai Hak Cuti',
            'resign_date' => 'Tanggal Keluar',
            'resign_reason' => 'Alasan Keluar',
            'annual_leave_quota' => 'Jatah Cuti Per Tahun (Hari)',
            'remaining_leave' => 'Sisa Cuti (Hari)',

            // Tab 3: Payroll & Pajak
            'npwp' => 'NPWP',
            'tax_method' => 'Metode Pajak',
            'bank_name' => 'Nama Bank',
            'bank_account_number' => 'Nomor Rekening',
            'bank_account_holder' => 'Atas Nama Rekening',

            // Tab 4: BPJS
            'bpjs_kesehatan_number' => 'No. BPJS Kesehatan',
            'bpjs_ketenagakerjaan_number' => 'No. BPJS Ketenagakerjaan',
            'bpjs_kesehatan' => 'BPJS Kesehatan',
            'bpjs_jht' => 'JHT (Jaminan Hari Tua)',
            'bpjs_jp' => 'JP (Jaminan Pensiun)',
            'bpjs_jkk' => 'JKK (Jaminan Kecelakaan Kerja)',
            'bpjs_jkm' => 'JKM (Jaminan Kematian)',
        ];
    }

    /**
     * Option Lists matching Form_Karyawan.html
     */
    public static function listGender()
    {
        return [
            'Laki-laki' => 'Laki-laki',
            'Perempuan' => 'Perempuan',
        ];
    }

    public static function listMaritalStatus()
    {
        return [
            'Belum menikah' => 'Belum menikah',
            'Menikah' => 'Menikah',
            'Cerai' => 'Cerai',
        ];
    }

    public static function listReligion()
    {
        return [
            'Islam' => 'Islam',
            'Kristen' => 'Kristen',
            'Katolik' => 'Katolik',
            'Hindu' => 'Hindu',
            'Buddha' => 'Buddha',
            'Konghucu' => 'Konghucu',
        ];
    }

    public static function listEducation()
    {
        return [
            'SMA/SMK' => 'SMA/SMK',
            'D3' => 'D3',
            'S1' => 'S1',
            'S2' => 'S2',
            'S3' => 'S3',
        ];
    }

    public static function listEmploymentStatus()
    {
        return [
            'Tetap' => 'Tetap',
            'Kontrak' => 'Kontrak',
            'Probation' => 'Probation',
            'Magang' => 'Magang',
            'Harian' => 'Harian',
        ];
    }

    public static function listResignReason()
    {
        return [
            'Resign' => 'Resign',
            'Habis kontrak' => 'Habis kontrak',
            'PHK' => 'PHK',
            'Pensiun' => 'Pensiun',
            'Lainnya' => 'Lainnya',
        ];
    }

    public static function listTaxMethod()
    {
        return [
            'Gross' => 'Gross',
            'Net' => 'Net',
            'Gross-up' => 'Gross-up',
        ];
    }

    public static function listBank()
    {
        return [
            'BCA' => 'BCA',
            'BRI' => 'BRI',
            'BNI' => 'BNI',
            'Mandiri' => 'Mandiri',
            'BSI' => 'BSI',
            'Lainnya' => 'Lainnya',
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
     * Gets query for [[Position]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPosition()
    {
        return $this->hasOne(Position::class, ['id_position' => 'id_position']);
    }

    /**
     * Gets query for [[Manager]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getManager()
    {
        return $this->hasOne(Account::class, ['id_user' => 'id_manager']);
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
            'TK/0' => 'A',
            'TK' => 'A',
            'TK/1' => 'A',
            'TK/2' => 'B',
            'TK/3' => 'B',
            'K/0' => 'A',
            'K/1' => 'B',
            'K/2' => 'B',
            'K/3' => 'C',
            'K/I/0' => 'B',
            'K/I/1' => 'B',
            'K/I/2' => 'C',
            'K/I/3' => 'C',
        ];

        if ($pairKey) {
            $keys = array_keys($kategoriPtkp);
            return array_combine($keys, $keys);
        }

        return $kategoriPtkp;
    }
}
