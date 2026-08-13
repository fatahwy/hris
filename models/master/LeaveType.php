<?php

namespace app\models\master;

use app\helpers\GeneralHelper;
use app\models\trx\LeaveRequest;
use app\models\BaseModel;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "leave_type".
 *
 * @property int $id_leave_type
 * @property int $id_company
 * @property string $name
 * @property string $category
 * @property int|null $max_day
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Company $company
 * @property LeaveRequest[] $leaveRequests
 */
class LeaveType extends BaseModel
{

    public $id_client;

    /**
     * ENUM field values
     */
    const CATEGORY_PERMISSION = 'PERMISSION';
    const CATEGORY_LEAVE = 'LEAVE';

    const P_SICK = 1;
    const P_LATE = 2;
    const P_BACKFIRST = 3;
    const P_LEAVEOFFICE = 4;
    const L_YEARLY = 11;

    const RES_ID = [self::P_SICK, self::P_LATE, self::P_BACKFIRST, self::P_LEAVEOFFICE, self::L_YEARLY];
    const P_ON_DUTY = [self::P_LATE, self::P_BACKFIRST, self::P_LEAVEOFFICE];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'leave_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['max_day'], 'default', 'value' => null],
            [['id_company', 'name', 'category'], 'required'],
            [['id_company', 'max_day', 'id_client'], 'integer'],
            [['category'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            ['category', 'in', 'range' => array_keys(self::optsCategory())],
            [['id_company'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['id_company' => 'id_company']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_leave_type' => 'Id Leave Type',
            'id_company' => 'Perusahaan',
            'name' => 'Nama',
            'category' => 'Kategori',
            'max_day' => 'Max Hari',
            'created_at' => 'Tgl Buat',
            'updated_at' => 'Tgl Update',
        ];
    }

    public function beforeSave($insert)
    {
        if (!$this->isNewRecord) {
            $this->category = $this->oldAttributes['category'];
        }
        return parent::beforeSave($insert);
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
     * Gets query for [[LeaveRequests]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLeaveRequests()
    {
        return $this->hasMany(LeaveRequest::class, ['id_leave_type' => 'id_leave_type']);
    }


    /**
     * column category ENUM value labels
     * @return string[]
     */
    public static function optsCategory()
    {
        return [
            self::CATEGORY_PERMISSION => 'Izin',
            self::CATEGORY_LEAVE => 'Cuti',
        ];
    }

    /**
     * @return string
     */
    public function displayCategory()
    {
        return self::optsCategory()[$this->category];
    }

    /**
     * @return bool
     */
    public function isCategoryPermission()
    {
        return $this->category === self::CATEGORY_PERMISSION;
    }

    public function setCategoryToPermission()
    {
        $this->category = self::CATEGORY_PERMISSION;
    }

    /**
     * @return bool
     */
    public function isCategoryLeave()
    {
        return $this->category === self::CATEGORY_LEAVE;
    }

    public function setCategoryToLeave()
    {
        $this->category = self::CATEGORY_LEAVE;
    }

    public static function getQueryByCompany($tableName = '')
    {
        $query = self::find()
            ->andWhere([
                'OR',
                ["leave_type.id_company" => GeneralHelper::session('id_company')],
                ["leave_type.id_leave_type" => self::RES_ID]
            ]);
            
        return $query;
    }

    public static function getList()
    {
        $models = self::getQueryByCompany()
            ->orderBy(['name' => SORT_ASC])
            ->all();

        return ArrayHelper::map($models, 'id_leave_type', 'name');
    }

}