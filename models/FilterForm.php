<?php

namespace app\models;

use app\components\Helper;

class FilterForm extends BaseModel
{
    public $id;
    public $day;
    public $month;
    public $year;
    public $name;
    public $date;
    public $date_start;
    public $date_end;
    public $id_client;
    public $id_company;
    public $type;
    public $id_category;
    public $status;
    public $created_at;
    public $id_province;
    public $id_city;
    public $id_district;
    public $id_subdistrict;
    public $barcode;
    public $no_batch;
    public $id_products;
    public $is_overtime;
    public $id_user;
    public $direction;
    public $target;
    public $total_achieve;
    public $is_achieve;
    public $percent_achieve;
    public $point;
    public $total;
    public $value;
    public $is_new;
    public $quantity;
    public $year_end;
    public $month_end;
    public $total_price_min;
    public $total_price_max;
    public $ref_no;

    public function __construct($fieldName = null)
    {
        parent::__construct();
        $this->type = $fieldName;
    }

    public function rules()
    {
        return [
            [['name', 'date', 'date_start', 'date_end', 'id_warehouse', 'id_rack', 'id_product', 'id_store', 'id_branch', 'id_package', 'id_package_1', 'invoice', 'is_consignment', 'id_category', 'payment_type', 'no_invoice', 'id_location', 'barcode', 'id_products', 'id_coa', 'ref_no'], 'safe'],
            [['no_sp', 'book_date', 'product_name', 'id_supplier', 'status'], 'safe'], // procurement
            [['type', 'created_at', 'id_patient', 'id_pharmacist', 'trx_type', 'id_job_vacancy', 'id_member', 'id_pic', 'total', 'is_new', 'quantity', 'total_price_min', 'total_price_max'], 'safe'],
            [['perspective_name', 'strategic_name', 'kpi_name', 'pic_name', 'division_name', 'direction', 'target', 'total_achieve', 'is_achieve', 'percent_achieve', 'priority', 'count_diff'], 'safe'], // report kpi
            [['pareto', 'no_batch', 'id_user', 'id', 'product_focus', 'patient_name', 'category_member', 'value', 'manufacture'], 'string'],
            [['id_province', 'id_city', 'id_district', 'id_subdistrict', 'via_branch', 'is_all_product', 'is_overtime', 'day', 'month', 'year', 'year_end', 'month_end', 'point', 'id_product_type', 'id_job', 'id_division', 'jenis_sppd', 'swamedic_type'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Nama',
            'id_branch' => 'Cabang',
            'is_overtime' => 'Tipe Presensi',
            'date_start' => 'Tgl Mulai',
            'date_end' => 'Tgl Selesai',
            'id_product' => 'Produk',
            'id_warehouse' => 'Gudang',
            'id_rack' => 'Rak',
            'product_name' => 'Produk',
            'is_consignment' => 'Barang Konsinyasi',
            'payment_type' => 'Tipe Bayar',
            'pareto' => 'Pareto',
            'id_province' => 'Provinsi',
            'id_city' => 'Kabupaten/Kota',
            'id_district' => 'Kecamatan',
            'id_subdistrict' => 'Desa',
            'trx_type' => 'Transaksi',
            'is_all_product' => 'Tipe Produk',
            'day' => 'Tanggal',
            'month' => 'Bulan',
            'year' => 'Tahun',
            'type' => 'Tipe',
            'perspective_name' => 'Perspective',
            'id_member' => 'Member',
            'id_pic' => 'PIC',
            'category_member' => 'Kategori Member',
            'id_division' => 'Divisi',
            'year_end' => 'Tahun Selesai',
            'month_end' => 'Bulan Selesai',
            'swamedic_type' => 'Tipe Swamedikasi',
        ];
    }

    public function load($data, $formName = null)
    {
        $flag = false;
        $scope = $formName === null ? $this->formName() : $formName;
        if ($scope === '' && !empty($data)) {
            $flag = true;
        } elseif (isset($data[$scope])) {
            $data = $data[$scope];
            $flag = true;
        }

        if ($flag) {
            $this->setAttributes($data);

            if ($this->no_sp) {
                $this->no_sp = Helper::numberOnly($this->no_sp);
            }

            return true;
        }

        return false;
    }
}