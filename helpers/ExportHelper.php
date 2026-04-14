<?php

namespace app\helpers;

use app\models\Account;
use app\models\asset\MstAsset;
use app\models\asset\MstAssetCategoryType;
use app\models\AuthItem;
use app\models\Master;
use app\models\MstDivision;
use app\models\MstDoseUnit;
use app\models\MstDrugCategory;
use app\models\MstInsurance;
use app\models\MstDiseaseType;
use app\models\MstJob;
use app\models\MstMember;
use app\models\MstPackage;
use app\models\MstPosition;
use app\models\MstProduct;
use app\models\MstProductType;
use app\models\MstRack;
use app\models\MstSediaan;
use app\models\MstSupplier;
use app\models\MstWarehouse;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class ExportHelper
{

    public static function templateProduct($id_branch, $branch_name, $fieldNames, $completeName, $status = 1)
    {
        $title = 'Template Produk Cabang ' . $branch_name;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Impor');

        $i = $start = 2;
        $sheet->setCellValue('A' . ($i - 1), 'Impor Produk Cabang ' . $branch_name);

        $row = $field = 'A';
        foreach ($fieldNames as $name) {
            $field = $row;
            $sheet->setCellValue($row . $i, $name);
            $row++;
        }

        $listYesNo = ['TIDAK', 'YA'];
        $listAktifNonAktif = ['NONAKTIF', 'AKTIF'];
        $models = MstProduct::find_all()
            ->innerJoinWith(['doseUnit', 'package1', 'productType'])
            ->with(['package2', 'package3', 'sediaan', 'drugCategory', 'drugPharmacology'])
            ->andWhere(['id_branch' => $id_branch])
            ->andFilterWhere(['mst_product.status' => $status])
            ->orderBy(['name' => SORT_ASC])
            ->all();

        foreach ($models as $index => $m) {
            $idx = $i + $index + 1;
            $sheet->setCellValue('A' . $idx, $m->id_product)
                ->setCellValue('B' . $idx, $completeName ? $m->name . ' (' . $m->dose . ' ' . $m->doseUnit->name . ')' : $m->name)
                ->setCellValue('C' . $idx, $m->barcode)
                ->setCellValue('D' . $idx, $m->productType->name)
                ->setCellValue('E' . $idx, $m->manufacture)
                ->setCellValue('F' . $idx, $m->dose)
                ->setCellValue('G' . $idx, $m->doseUnit->name)
                ->setCellValue('H' . $idx, $m->package1->name)
                ->setCellValue('I' . $idx, $m->package2->name ?? NULL)
                ->setCellValue('J' . $idx, $m->package_2_amount)
                ->setCellValue('K' . $idx, $m->package3->name ?? NULL)
                ->setCellValue('L' . $idx, $m->package_3_amount)
                ->setCellValue('M' . $idx, $m->purchase_price)
                ->setCellValue('N' . $idx, $m->margin_persen)
                ->setCellValue('O' . $idx, $m->selling_price)
                ->setCellValue('P' . $idx, $m->selling_price_receipt)
                ->setCellValue('Q' . $idx, $m->sediaan->name ?? NULL)
                ->setCellValue('R' . $idx, $m->drugCategory->name ?? NULL)
                ->setCellValue('S' . $idx, $m->drugPharmacology->name ?? NULL)
                ->setCellValue('T' . $idx, !empty($listYesNo[$m->is_drug_generic]) ? $listYesNo[$m->is_drug_generic] : null)
                ->setCellValue('U' . $idx, $listAktifNonAktif[$m->status])
                ->setCellValue('V' . $idx, $listYesNo[$m->is_free_sell])
                ->setCellValue('W' . $idx, $listYesNo[$m->is_consignment])
                ->setCellValue('X' . $idx, $m->content);
            setDropdown($sheet, "T$idx", '"' . implode(',', $listYesNo) . '"');
            setDropdown($sheet, "U$idx", '"' . implode(',', $listAktifNonAktif) . '"');
            setDropdown($sheet, "V$idx", '"' . implode(',', $listYesNo) . '"');
            setDropdown($sheet, "W$idx", '"' . implode(',', $listYesNo) . '"');
        }

        self::setAutoSize($sheet, $field, $start, $i);

        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Master')
            ->setCellValue('A' . ($i - 1), 'Data Referensi')
            ->setCellValue('A' . $i, 'Tipe Produk')
            ->setCellValue('B' . $i, 'Satuan Dosis')
            ->setCellValue('C' . $i, 'Kemasan')
            ->setCellValue('D' . $i, 'Sediaan')
            ->setCellValue('E' . $i, 'Golongan Keamanan')
            ->setCellValue('F' . $i, 'Golongan Farmakologi')
            ->setCellValue('G' . $i, 'Obat Generik')
            ->setCellValue('H' . $i, 'Status')
            ->setCellValue('I' . $i, 'Bebas Dijual')
            ->setCellValue('J' . $i, 'Barang Konsinyasi');

        foreach (MstProductType::find()->all() as $index => $m) {
            $sheet2->setCellValue('A' . ($i + $index + 1), $m->name);
        }

        foreach (MstDoseUnit::find()->all() as $index => $m) {
            $sheet2->setCellValue('B' . ($i + $index + 1), $m->name);
        }

        foreach (MstPackage::find()->all() as $index => $m) {
            $sheet2->setCellValue('C' . ($i + $index + 1), $m->name);
        }

        foreach (MstSediaan::find()->all() as $index => $m) {
            $sheet2->setCellValue('D' . ($i + $index + 1), $m->name);
        }

        foreach (MstDrugCategory::find()->where(['type' => 0])->all() as $index => $m) {
            $sheet2->setCellValue('E' . ($i + $index + 1), $m->name);
        }

        foreach (MstDrugCategory::find()->where(['type' => 1])->all() as $index => $m) {
            $sheet2->setCellValue('F' . ($i + $index + 1), $m->name);
        }

        $sheet2->setCellValue('G' . ($i + 1), 'YA')
            ->setCellValue('G' . ($i + 2), 'TIDAK')
            ->setCellValue('H' . ($i + 1), 'AKTIF')
            ->setCellValue('H' . ($i + 2), 'NONAKITF')
            ->setCellValue('I' . ($i + 1), 'YA')
            ->setCellValue('I' . ($i + 2), 'TIDAK')
            ->setCellValue('J' . ($i + 1), 'YA')
            ->setCellValue('J' . ($i + 2), 'TIDAK');

        self::doExport($title, $spreadsheet);
        die;
    }

    public static function templateProductSupplier($id_branch, $branch_name, $fieldNames)
    {
        $title = 'Template Produk Supplier Cabang ' . $branch_name;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Impor');

        $i = $start = 2;
        $sheet->setCellValue('A' . ($i - 1), 'Update Produk Supplier Cabang ' . $branch_name);

        $row = $field = 'A';
        foreach ($fieldNames as $name) {
            $field = $row;
            $sheet->setCellValue($row . $i, $name);
            $row++;
        }

        $models = MstProduct::find()
            ->innerJoinWith(['doseUnit'])
            ->joinWith(['supplier'])
            ->andWhere(['id_branch' => $id_branch])
            ->orderBy(['name' => SORT_ASC])
            ->all();

        foreach ($models as $index => $m) {
            $idx = $i + $index + 1;
            $sheet->setCellValue('A' . $idx, $m->id_product)
                ->setCellValue('B' . $idx, $m->name . ' (' . $m->dose . ' ' . $m->doseUnit->name . ')')
                ->setCellValue('C' . $idx, $m->supplier->name ?? '');
        }

        self::setAutoSize($sheet, $field, $start, $i);

        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Master')
            ->setCellValue('A' . ($i - 1), 'Data Referensi')
            ->setCellValue('A' . $i, 'Supplier');

        $modelsSupplier = Yii::$app->cache->getOrSet("listSupplier", function () {
            return MstSupplier::find()->all();
        });

        foreach ($modelsSupplier as $index => $m) {
            $sheet2->setCellValue('A' . ($i + $index + 1), $m->name);
        }

        self::doExport($title, $spreadsheet);
        die;
    }

    public static function templateSupplier($fieldNames)
    {
        $title = 'Template Suplier';
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Impor');

        $i = $start = 2;
        $sheet->setCellValue('A' . ($i - 1), 'Impor Suplier');

        $row = $field = 'A';
        foreach ($fieldNames as $name) {
            $field = $row;
            $sheet->setCellValue($row . $i, $name);
            $row++;
        }

        $modelsSupplier = Yii::$app->cache->getOrSet("listSupplier", function () {
            return MstSupplier::find()->all();
        });

        foreach ($modelsSupplier as $index => $m) {
            $sheet->setCellValue('A' . ($i + $index + 1), $m->id_supplier)
                ->setCellValue('B' . ($i + $index + 1), $m->name)
                ->setCellValue('C' . ($i + $index + 1), $m->address)
                ->setCellValue('D' . ($i + $index + 1), $m->no_telp)
                ->setCellValue('E' . ($i + $index + 1), $m->email)
                ->setCellValue('F' . ($i + $index + 1), $m->long_due_date);
        }

        self::setAutoSize($sheet, $field, $start, $i);
        self::doExport($title, $spreadsheet);
        die;
    }

    public static function templateDiseaseType($fieldNames)
    {
        $title = 'Template Jenis Penyakit';
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Impor');

        $i = $start = 2;
        $sheet->setCellValue('A' . ($i - 1), 'Impor Jenis Penyakit');

        $row = $field = 'A';
        foreach ($fieldNames as $name) {
            $field = $row;
            $sheet->setCellValue($row . $i, $name);
            $row++;
        }

        foreach (MstDiseaseType::find()->all() as $index => $m) {
            $sheet->setCellValue('A' . ($i + $index + 1), $m->id_disease_type)
                ->setCellValue('B' . ($i + $index + 1), $m->name)
                ->setCellValue('C' . ($i + $index + 1), $m->note);
        }

        self::setAutoSize($sheet, $field, $start, $i);
        self::doExport($title, $spreadsheet);
        die;
    }

    public static function templatePackage($fieldNames)
    {
        $title = 'Template Kemasan';
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Impor');

        $i = $start = 2;
        $sheet->setCellValue('A' . ($i - 1), 'Impor Kemasan');

        $row = $field = 'A';
        foreach ($fieldNames as $name) {
            $field = $row;
            $sheet->setCellValue($row . $i, $name);
            $row++;
        }

        foreach (MstPackage::find()->all() as $index => $m) {
            $sheet->setCellValue('A' . ($i + $index + 1), $m->id_package)
                ->setCellValue('B' . ($i + $index + 1), $m->name)
                ->setCellValue('C' . ($i + $index + 1), $m->note);
        }

        self::setAutoSize($sheet, $field, $start, $i);
        self::doExport($title, $spreadsheet);
        die;
    }

    public static function templateDoseUnit($fieldNames)
    {
        $title = 'Template Satuan';
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Impor');

        $i = $start = 2;
        $sheet->setCellValue('A' . ($i - 1), 'Impor Satuan');

        $row = $field = 'A';
        foreach ($fieldNames as $name) {
            $field = $row;
            $sheet->setCellValue($row . $i, $name);
            $row++;
        }

        foreach (MstDoseUnit::find()->all() as $index => $m) {
            $sheet->setCellValue('A' . ($i + $index + 1), $m->id_dose_unit)
                ->setCellValue('B' . ($i + $index + 1), $m->name)
                ->setCellValue('C' . ($i + $index + 1), $m->note);
        }

        self::setAutoSize($sheet, $field, $start, $i);
        self::doExport($title, $spreadsheet);
        die;
    }

    public static function templateSediaan($fieldNames)
    {
        $title = 'Template Sediaan';
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Impor');

        $i = $start = 2;
        $sheet->setCellValue('A' . ($i - 1), 'Impor Sediaan');

        $row = $field = 'A';
        foreach ($fieldNames as $name) {
            $field = $row;
            $sheet->setCellValue($row . $i, $name);
            $row++;
        }

        foreach (MstSediaan::find()->all() as $index => $m) {
            $sheet->setCellValue('A' . ($i + $index + 1), $m->id_sediaan)
                ->setCellValue('B' . ($i + $index + 1), $m->name);
        }

        self::setAutoSize($sheet, $field, $start, $i);
        self::doExport($title, $spreadsheet);
        die;
    }

    public static function templateMember($idBranch, $branch_name, $fieldNames)
    {
        $title = "Template Member Cabang $branch_name";
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Impor');

        $i = $start = 2;
        $sheet->setCellValue('A' . ($i - 1), "Impor Member Cabang $branch_name");

        $row = $field = 'A';
        foreach ($fieldNames as $name) {
            $field = $row;
            $sheet->setCellValue($row . $i, $name);
            $row++;
        }

        //['ID Member', 'Nama', 'JK', 'Tgl Lahir', 'Gol Darah', 'Tempat Lahir', 'Profesi', 'Telpon', 'NPWP', 'Alamat', 'Email', 'Asuransi', 'No Asuransi'];
        foreach (MstMember::find()->andWhere(['id_branch' => $idBranch])->all() as $index => $m) {
            $sheet->setCellValue('A' . ($i + $index + 1), $m->id_member)
                ->setCellValue('B' . ($i + $index + 1), $m->name)
                ->setCellValue('C' . ($i + $index + 1), $m->sex)
                ->setCellValue('D' . ($i + $index + 1), $m->dob)
                ->setCellValue('E' . ($i + $index + 1), $m->blood_group)
                ->setCellValue('F' . ($i + $index + 1), $m->pob)
                ->setCellValue('G' . ($i + $index + 1), $m->id_job)
                ->setCellValue('H' . ($i + $index + 1), $m->telp)
                ->setCellValue('I' . ($i + $index + 1), $m->npwp)
                ->setCellValue('J' . ($i + $index + 1), $m->address)
                ->setCellValue('K' . ($i + $index + 1), $m->email)
                ->setCellValue('L' . ($i + $index + 1), $m->id_insurance)
                ->setCellValue('M' . ($i + $index + 1), $m->no_insurance);
        }

        self::setAutoSize($sheet, $field, $start, $i);

        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Master')
            ->setCellValue('A' . ($i - 1), 'Data Referensi')
            ->setCellValue('A' . $i, 'ID Profesi')
            ->setCellValue('B' . $i, 'Nama')
            ->setCellValue('D' . $i, 'ID Asuransi')
            ->setCellValue('E' . $i, 'Nama');

        foreach (MstJob::find()->all() as $index => $m) {
            $sheet2->setCellValue('A' . ($i + $index + 1), $m->id_job)
                ->setCellValue('B' . ($i + $index + 1), $m->name);
        }

        foreach (MstInsurance::find()->all() as $index => $m) {
            $sheet2->setCellValue('D' . ($i + $index + 1), $m->id_insurance)
                ->setCellValue('E' . ($i + $index + 1), $m->name);
        }

        self::doExport($title, $spreadsheet);
        die;
    }

    public static function templateUser($id_branch, $branch_name, $fieldNames)
    {
        $user = GeneralHelper::identity();

        $title = 'Template User Cabang ' . $branch_name;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Impor');

        $i = $start = 2;
        $sheet->setCellValue('A' . ($i - 1), 'Impor User Cabang ' . $branch_name);

        $row = $field = 'A';
        foreach ($fieldNames as $name) {
            $field = $row;
            $sheet->setCellValue($row . $i, $name);
            $row++;
        }

        $modelUser = Account::find()
            ->innerJoinWith(['role'])
            ->andWhere(['id_branch' => $id_branch])
            ->all();

        // ['Username', 'Password', 'Role', 'Email', 'Name', 'Nip', 'No Telp', 'Cabang'];
        foreach ($modelUser as $index => $m) {
            $sheet->setCellValue('A' . ($i + $index + 1), $m->username)
                ->setCellValue('B' . ($i + $index + 1), NULL)
                ->setCellValue('C' . ($i + $index + 1), $m->role->item_name)
                ->setCellValue('D' . ($i + $index + 1), $m->email)
                ->setCellValue('E' . ($i + $index + 1), $m->name)
                ->setCellValue('F' . ($i + $index + 1), $m->nip)
                ->setCellValue('G' . ($i + $index + 1), $m->no_telp);
        }

        self::setAutoSize($sheet, $field, $start, $i);

        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Master')
            ->setCellValue('A' . ($i - 1), 'Data Referensi')
            ->setCellValue('A' . $i, 'Role');

        foreach (AuthItem::getList() as $role) {
            $i++;
            $sheet2->setCellValue('A' . $i, $role);
        }

        self::doExport($title, $spreadsheet);
        die;
    }

    public static function templateEmployee($id_branch, $branch_name, $fieldNames)
    {
        $user = GeneralHelper::identity();
        $id_store = $user->id_store;

        $title = 'Template Pegawai Cabang ' . $branch_name;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Impor');

        $i = $start = 2;
        $sheet->setCellValue('A' . ($i - 1), 'Impor Pegawai Cabang ' . $branch_name);

        $row = $field = 'A';
        foreach ($fieldNames as $name) {
            $field = $row;
            $sheet->setCellValue($row . $i, $name);
            $row++;
        }

        $modelUser = Account::find()
            ->innerJoinWith(['role'])
            ->andWhere(['id_branch' => $id_branch])
            ->all();

        $listGender = ['Perempuan', 'Laki - Laki'];
        $listEmployeeStat = Master::employeeStat();
        $listDivision = ArrayHelper::getColumn(MstDivision::find()->all(), 'name');
        $listPosition = ArrayHelper::getColumn(MstPosition::find()->all(), 'name');

        // 'Username*', 'Password (Isi jika ingin merubah)*', 'Role*', 'Email', 'Nama*', 'Nip', 'No Telp',
        // 'Tgl Gabung*', 'Tgl Lahir', 'Tempat Tgl Lahir', 'Nama Ibu Kandung', 'Pendidikan Terkahir', 'Status Perkawinan', 'Jenis Kelmain',
        // 'Nama Kerabat Terdekat', 'Alamat Kerabat Terdekat', 'No Telp/WA Kerabat Terdekat', 'Nama Bank', 'No Rekening*',
        // 'Jabatan', 'Divisi', 'Status Pegawai', 'Tgl Kontrak Berakhir', 'Gaji Pokok (Rp)', 'Kenaikan Gapok(%)', 'Tunjangan Makan (Rp)', 'Tunjangan Transportasi (Rp)',
        // 'Tunjungan Keluarga (Rp)', 'Tunjungan Jabatan (Rp)', 'Tunjungan Pendidikan (Rp)', 'BPJS (Rp)',
        $idx = $i;
        foreach ($modelUser as $index => $m) {
            $idx++;

            $sheet->setCellValue("A$idx", $m->username)
                ->setCellValue("B$idx", NULL)
                ->setCellValue("C$idx", $m->role->item_name)
                ->setCellValue("D$idx", $m->email)
                ->setCellValue("E$idx", $m->name)
                ->setCellValueExplicit("F$idx", $m->nip, DataType::TYPE_STRING)
                ->setCellValueExplicit("G$idx", $m->no_telp, DataType::TYPE_STRING)
                ->setCellValue("H$idx", $m->join_date)
                ->setCellValue("I$idx", $m->dob)
                ->setCellValue("J$idx", $m->pob)
                ->setCellValue("K$idx", $m->mother_name)
                ->setCellValue("L$idx", $m->last_education)
                ->setCellValue("M$idx", Account::getListMaritalStatus()[$m->marital_status] ?? null)
                ->setCellValue("N$idx", $listGender[$m->gender] ?? null)
                ->setCellValue("O$idx", $m->contact_family_name)
                ->setCellValue("P$idx", $m->contact_family_address)
                ->setCellValue("Q$idx", $m->contact_family_no_telp)
                ->setCellValue("R$idx", $m->bank_name)
                ->setCellValue("S$idx", $m->account_number)

                ->setCellValue("T$idx", $m->position->name ?? null)
                ->setCellValue("U$idx", $m->division->name ?? null)
                ->setCellValue("V$idx", $listEmployeeStat[$m->employee_stat] ?? null)
                ->setCellValue("W$idx", $m->end_contract)
                ->setCellValue("X$idx", $m->basic_salary)
                ->setCellValue("Y$idx", $m->salary_increase_percen)
                ->setCellValue("Z$idx", $m->meal_allowance)
                ->setCellValue("AA$idx", $m->transport_allowance)
                ->setCellValue("AB$idx", $m->family_allowance)
                ->setCellValue("AC$idx", $m->position_allowance)
                ->setCellValue("AD$idx", $m->education_allowance)
                ->setCellValue("AE$idx", $m->bpjs)
                ->setCellValue("AF$idx", $m->insentive);
        }

        for ($j = $i + 1; $j < $idx + 25; $j++) {
            setDropdown($sheet, "C$j", '"' . implode(',', AuthItem::getList()) . '"');
            setDropdown($sheet, "M$j", '"' . implode(',', Account::getListMaritalStatus()) . '"');
            setDropdown($sheet, "N$j", '"' . implode(',', $listGender) . '"');
            setDropdown($sheet, "T$j", '"' . implode(',', $listPosition) . '"');
            setDropdown($sheet, "U$j", '"' . implode(',', $listDivision) . '"');
            setDropdown($sheet, "V$j", '"' . implode(',', $listEmployeeStat) . '"');
        }

        self::setAutoSize($sheet, $field, $start, $i);
        self::doExport($title, $spreadsheet);
        die;
    }

    public static function templateDrugCategory($fieldNames)
    {
        $title = 'Template Golongan';
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Impor');

        $i = $start = 2;
        $sheet->setCellValue('A' . ($i - 1), 'Impor Golongan');

        $row = $field = 'A';
        foreach ($fieldNames as $name) {
            $field = $row;
            $sheet->setCellValue($row . $i, $name);
            $row++;
        }

        foreach (MstDrugCategory::find()->all() as $index => $m) {
            $sheet->setCellValue('A' . ($i + $index + 1), $m->id_drug_category)
                ->setCellValue('B' . ($i + $index + 1), $m->name)
                ->setCellValue('C' . ($i + $index + 1), $m->description)
                ->setCellValue('D' . ($i + $index + 1), $m->type);
        }

        self::setAutoSize($sheet, $field, $start, $i);

        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Master')
            ->setCellValue('A' . ($i - 1), 'Data Referensi')
            ->setCellValue('A' . $i, 'ID Golongan')
            ->setCellValue('B' . $i, 'Nama');

        foreach (GeneralHelper::textDrugCategory() as $index => $value) {
            $sheet2->setCellValue('A' . ($i + $index + 1), $index)
                ->setCellValue('B' . ($i + $index + 1), $value);
        }

        self::doExport($title, $spreadsheet);
        die;
    }

    public static function templateRack($id_branch, $branch_name, $fieldNames)
    {
        $title = 'Template Rak Cabang ' . $branch_name;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Impor');

        $i = $start = 2;
        $sheet->setCellValue('A' . ($i - 1), 'Impor Rak Cabang ' . $branch_name);

        $row = $field = 'A';
        foreach ($fieldNames as $name) {
            $field = $row;
            $sheet->setCellValue($row . $i, $name);
            $row++;
        }

        foreach (MstRack::findAll(['id_branch' => $id_branch]) as $index => $m) {
            $sheet->setCellValue('A' . ($i + $index + 1), $m->id_rack)
                ->setCellValue('B' . ($i + $index + 1), $m->name)
                ->setCellValue('C' . ($i + $index + 1), $m->description);
        }

        self::setAutoSize($sheet, $field, $start, $i);
        self::doExport($title, $spreadsheet);
        die;
    }

    public static function templateWarehouse($id_branch, $branch_name, $fieldNames)
    {
        $title = 'Template Gudang Cabang ' . $branch_name;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Impor');

        $i = $start = 2;
        $sheet->setCellValue('A' . ($i - 1), 'Impor Gudang Cabang ' . $branch_name);

        $row = $field = 'A';
        foreach ($fieldNames as $name) {
            $field = $row;
            $sheet->setCellValue($row . $i, $name);
            $row++;
        }

        foreach (MstWarehouse::findAll(['id_branch' => $id_branch]) as $index => $m) {
            $sheet->setCellValue('A' . ($i + $index + 1), $m->id_warehouse)
                ->setCellValue('B' . ($i + $index + 1), $m->name)
                ->setCellValue('C' . ($i + $index + 1), $m->description);
        }

        self::setAutoSize($sheet, $field, $start, $i);
        self::doExport($title, $spreadsheet);
        die;
    }

    public static function templateStockOpname($id_branch, $branch_name, $fieldNames)
    {
        $title = 'Template Stock Opname Cabang ' . $branch_name;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Impor');

        $sheet->setCellValue('A1', 'Catatan: ');
        $sheet->setCellValue('A2', '1. Silahkan update/isi kolom yang berwarna orange');
        $sheet->setCellValue('A3', '2. Hapus baris data jika produk tidak ingin diimpor');
        $sheet->mergeCells('A1:C1');
        $sheet->mergeCells('A2:C2');
        $sheet->mergeCells('A3:C3');

        $i = $start = 6;
        $row = $field = 'A';
        $sheet->setCellValue($field . ($i - 1), 'Impor Stock Opname Cabang ' . $branch_name);

        foreach ($fieldNames as $name) {
            $field = $row;
            $sheet->setCellValue($row . $i, $name);
            $row++;
        }

        $qpe = DBHelper::getProductExpired($id_branch)
            ->andWhere(['>', 'expired_date', DBHelper::today()]);

        $sq = (new Query())
            ->from([
            'x' => (new Query())
            ->select([new Expression('ROW_NUMBER() OVER(PARTITION BY id_product ORDER BY id_product asc, expired_date asc) AS num'), 'x.*'])
            ->from(['x' => $qpe])
        ])
            ->where(['num' => 1]);

        $models = DBHelper::getProductStock($id_branch, true)
            ->select(['ps.*', 'e.expired_date', 'du.name as dose_name', 'r.name as rack_name', 'w.name as warehouse_name'])
            ->innerJoin(['du' => 'mst_dose_unit'], 'du.id_dose_unit = ps.id_dose_unit')
            ->leftJoin(['e' => $sq], 'ps.id_product = e.id_product')
            ->leftJoin(['r' => 'mst_rack'], 'r.id_rack = ps.id_rack')
            ->leftJoin(['w' => 'mst_warehouse'], 'w.id_warehouse = ps.id_warehouse')
            ->andWhere([
            'or',
            ['w.id_branch' => $id_branch],
            ['r.id_branch' => $id_branch]
        ])
            ->orderBy(['id_warehouse' => SORT_ASC, 'id_rack' => SORT_ASC, 'ps.name' => SORT_ASC])
            ->all();

        foreach ($models as $index => $m) {
            $idx = $i + $index + 1;
            $sheet->setCellValue('A' . $idx, $m['id_product'])
                ->setCellValue('B' . $idx, $m['warehouse_name'] ?? NULL)
                ->setCellValue('C' . $idx, $m['rack_name'] ?? NULL)
                ->setCellValue('D' . $idx, $m['name'] . ' (' . $m['dose'] . ' ' . $m['dose_name'] . ' )')
                // ->setCellValue('E' . $idx, $m->total)
                ->setCellValue('G' . $idx, $m['expired_date']);

            self::requiredCell($sheet, 'E' . $idx);
            colorFilled($sheet, 'E' . $idx);
            colorFilled($sheet, 'F' . $idx);
            colorFilled($sheet, 'G' . $idx);
        }

        self::setAutoSize($sheet, $field, $start, $i);

        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Master')
            ->setCellValue('A' . ($i - 1), 'Data Referensi')
            ->setCellValue('A' . $i, 'Gudang')
            ->setCellValue('B' . $i, 'Rak');

        foreach (MstWarehouse::findAll(['id_branch' => $id_branch]) as $index => $m) {
            $sheet2->setCellValue('A' . ($i + $index + 1), $m->name);
        }

        foreach (MstRack::findAll(['id_branch' => $id_branch]) as $index => $m) {
            $sheet2->setCellValue('B' . ($i + $index + 1), $m->name);
        }

        self::doExport($title, $spreadsheet);
        die;
    }

    public static function templateReception($id_branch, $branch_name, $fieldNames, $konsi)
    {
        $title = 'Penerimaan Cabang ' . $branch_name . ' Produk ' . ($konsi ? 'Konsi' : 'Non Konsi');
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Impor');

        $sheet->setCellValue('D2', 'Catatan: ');
        $sheet->setCellValue('D3', '1. Silahkan update/isi kolom yang berwarna orange');
        $sheet->setCellValue('D4', '2. Hapus baris data jika produk tidak ingin diimpor');

        $sheet->setCellValue('A1', $konsi ? 'KONSI' : 'NONKONSI');
        $sheet->setCellValue('A2', 'Supplier');

        colorFilled($sheet, 'B2');
        self::requiredCell($sheet, 'B2');
        $sheet->setCellValue('A3', 'Jenis Pembayaran');
        $sheet->setCellValue('B3', 'TUNAI');
        setDropdown($sheet, 'B3', '"KREDIT, TUNAI"');
        colorFilled($sheet, 'B3');
        self::requiredCell($sheet, 'B3');
        $sheet->setCellValue('A4', 'No Faktur');
        colorFilled($sheet, 'B4');
        self::requiredCell($sheet, 'B4');
        $sheet->setCellValue('A5', 'Tgl Faktur');
        $sheet->setCellValue('B5', DBHelper::today());
        colorFilled($sheet, 'B5');
        self::requiredCell($sheet, 'B5');
        $sheet->setCellValue('A6', 'Tgl Penerimaan');
        $sheet->setCellValue('B6', DBHelper::today());
        colorFilled($sheet, 'B6');
        self::requiredCell($sheet, 'B6');
        $sheet->setCellValue('A7', 'Tgl Jatuh Tempo');
        colorFilled($sheet, 'B7');

        $i = $start = 10;
        $sheet->setCellValue('A' . ($i - 1), 'Impor ' . $title);
        $row = $field = 'A';
        foreach ($fieldNames as $name) {
            $field = $row;
            $sheet->setCellValue($row . $i, $name);
            $row++;
        }

        $models = MstProduct::find()
            ->innerJoinWith(['doseUnit', 'package1'])
            ->andWhere(['mst_product.id_branch' => $id_branch, 'status' => GeneralHelper::STAT_ACTIVE, 'is_consignment' => $konsi])
            ->orderBy(['mst_product.name' => SORT_ASC])
            ->all();

        $length = count($models) + $start;

        foreach ($models as $index => $m) {
            $sheet->setCellValue('A' . ($i + $index + 1), $m->id_product)
                ->setCellValue('B' . ($i + $index + 1), $m->name . ' (' . $m->dose . ' ' . $m->doseUnit->name . ' )')
                ->setCellValue('D' . ($i + $index + 1), $m->package1->name)
                ->setCellValue('G' . ($i + $index + 1), $m->purchase_price)
                ->setCellValue('H' . ($i + $index + 1), $m->selling_price);

            self::requiredCell($sheet, 'C' . ($i + $index + 1));
            self::requiredCell($sheet, 'E' . ($i + $index + 1));
            self::requiredCell($sheet, 'G' . ($i + $index + 1));
            self::requiredCell($sheet, 'H' . ($i + $index + 1));
        }
        colorFilled($sheet, "C$start:C$length");
        colorFilled($sheet, "E$start:E$length");
        colorFilled($sheet, "F$start:F$length");
        colorFilled($sheet, "G$start:G$length");
        colorFilled($sheet, "H$start:H$length");

        self::setAutoSize($sheet, $field, $start, $i);

        $i = 2;
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Master')
            ->setCellValue('A' . ($i - 1), 'Data Referensi')
            ->setCellValue('A' . $i, 'Supplier');

        $modelsSupplier = Yii::$app->cache->getOrSet("listSupplier", function () {
            return MstSupplier::find()->all();
        });

        foreach ($modelsSupplier as $index => $m) {
            $sheet2->setCellValue('A' . ($i + $index + 1), $m->name);
        }

        self::doExport("Template $title", $spreadsheet);
        die;
    }

    public static function templateAsset($id_branch, $branch_name, $fieldNames)
    {
        $title = "Aset $branch_name";
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Impor');

        $sheet->setCellValue('A1', 'Catatan: ')
            ->setCellValue('A2', '1. Silahkan update/isi kolom selain ID Aset')
            ->setCellValue('A3', '2. Hapus baris data jika aset tidak ingin diimpor')
            ->setCellValue('A4', '3. Aset baru tidak perlu diisikan ID Asset')
            ->setCellValue('A5', '4. Untuk kolom Tipe, Divisi Penggunaan, User Penanggung Jawab harus sesuai dengan data yang ada di sheet Master')
            ->mergeCells('A1:C1')
            ->mergeCells('A2:C2')
            ->mergeCells('A3:C3')
            ->mergeCells('A4:C4')
            ->mergeCells('A5:C5')
            ;

        $i = $start = 8;
        $sheet->setCellValue('A' . ($i - 1), 'Impor ' . $title);
        $row = $field = 'A';
        foreach ($fieldNames as $name) {
            $field = $row;
            $sheet->setCellValue($row . $i, $name);
            $row++;
        }

        $models = MstAsset::findData()
            ->with(['userResponsible', 'category', 'type', 'division'])
            ->andWhere(['stat.id_branch' => $id_branch])
            ->andWhere(['stat.status' => [MstAsset::STAT_ACTIVE, MstAsset::STAT_REPAIR]])
            ->orderBy(['mst_asset.name' => SORT_ASC])
            ->all();

        foreach ($models as $index => $m) {
            $idx = $i + $index + 1;
            $categoryType = $m->category->name . '|' . $m->type->name;

            $sheet->setCellValue("A$idx", $m->id_asset)
                ->setCellValue("B$idx", $m->name)
                ->setCellValue("C$idx", $categoryType)
                ->setCellValue("D$idx", $m->brand)
                ->setCellValue("E$idx", $m->spec)
                ->setCellValue("F$idx", $m->serial_number)
                ->setCellValue("G$idx", $m->period_maintain)
                ->setCellValue("H$idx", $m->purchase_price)
                ->setCellValue("I$idx", $m->purchase_date)
                ->setCellValue("J$idx", $m->economic_age)
                ->setCellValue("K$idx", $m->division->name)
                ->setCellValue("L$idx", $m->userResponsible->name);
        }

        self::setAutoSize($sheet, $field, $start, $i);

        // SHEET 2
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Master')
            ->setCellValue('A1', 'Tipe')
            ->setCellValue('B1', 'Divisi Penggunaan')
            ->setCellValue('C1', 'User Penanggung Jawab');

        $i = 2;
        // list tipe
        $arrTypes = [];
        $modelsTypes = MstAssetCategoryType::find()
            ->innerJoinWith(['category'])
            ->where(['not', ['mst_asset_category_type.id_parent' => null]])
            ->orderBy(['category.name' => SORT_ASC, 'mst_asset_category_type.name' => SORT_ASC])
            ->all();
        foreach ($modelsTypes as $index => $m) {
            $idx = $i + $index;
            $categoryType = $m->category->name . '|' . $m->name;
            $sheet2->setCellValue("A$idx", $categoryType);
            $arrTypes[] = $categoryType;
        }
        $arrTypes = implode(',', $arrTypes);

        // list divisi
        $arrListType = [];
        $modelsTypes = MstDivision::find()
            ->orderBy(['name' => SORT_ASC])
            ->all();
        foreach ($modelsTypes as $index => $m) {
            $idx = $i + $index;
            $sheet2->setCellValue("B$idx", $m->name);
            $arrListType[] = $m->name;
        }
        $arrListType = implode(',', $arrListType);

        // list user
        $arrUser = [];
        $modelsTypes = Account::find()
            ->where(['id_branch' => $id_branch, 'status' => GeneralHelper::STAT_ACTIVE])
            ->orderBy(['name' => SORT_ASC])
            ->all();
        foreach ($modelsTypes as $index => $m) {
            $idx = $i + $index;
            $sheet2->setCellValue("C$idx", $m->name);
            $arrUser[] = $m->name;
        }
        $arrUser = implode(',', $arrUser);

        for ($i = $start + 1; $i < 1000; $i++) {
            setDropdown($sheet, "C$i", '"' . $arrTypes . '"');
            setDropdown($sheet, "K$i", '"' . $arrListType . '"');
            setDropdown($sheet, "L$i", '"' . $arrUser . '"');
        }

        colorFilled($sheet2, "A1:C1");
        $spreadsheet->setActiveSheetIndex(0);

        self::doExport("Template $title", $spreadsheet);
        die;
    }

    public static function requiredCell($sheet, $cell)
    {
        $sheet->getCell($cell)
            ->getDataValidation()
            ->setAllowBlank(false)
            ->setShowInputMessage(true)
            ->setPrompt('Harus diisi');
    }

    public static function doExport($title, $spreadsheet)
    {
        header('Content-Type: application/vnd.ms-excel');
        $filename = str_replace(' ', '_', $title) . "_" . date("d_m_Y_His") . ".xls";
        header('Content-Disposition: attachment;filename=' . $filename . ' ');
        header('Cache-Control: max-age=0');
        $objWriter = IOFactory::createWriter($spreadsheet, 'Xls');

        $objWriter->save('php://output');
    }

    public static function setAutoSize($sheet, $last, $i = 13, $j = 13)
    {
        $title = [
            'font' => array(
                'bold' => true,
                'size' => 16
            ),
            'alignment' => array(
                'horizontal' => Alignment::HORIZONTAL_CENTER_CONTINUOUS,
            ),
        ];
        $header = [
            'font' => array(
                'bold' => true,
            ),
            'fill' => array(
                'fillType' => Fill::FILL_SOLID,
                'color' => array('argb' => '899DCF00'),
            ),
            'alignment' => array(
                'horizontal' => Alignment::HORIZONTAL_CENTER_CONTINUOUS,
            ),
        ];
        $border = [
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => array('argb' => '000000'),
                ),
            )
        ];
        $tt = $i - 1;
        $sheet->getStyle("A$tt:$last$tt")->applyFromArray($title);
        $sheet->getStyle("A$i:$last$i")->applyFromArray($header);
        $sheet->getStyle("A$i:$last$j")->applyFromArray($border);
        $abc = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD'];
        $stop = false;
        foreach ($abc as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
            if ($col == $last) {
                $stop = TRUE;
                break;
            }
        }

        if (!$stop) {
            foreach ($abc as $i) {
                foreach ($abc as $j) {
                    $col = $i . $j;
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                    if ($col == $last) {
                        return;
                    }
                }
            }
        }
    }
}

function colorFilled($sheet, $range)
{
    $style = [
        'fill' => array(
            'fillType' => Fill::FILL_SOLID,
            'color' => array('argb' => 'FFA500'),
        ),
        'borders' => array(
            'allBorders' => array(
                'borderStyle' => Border::BORDER_THIN,
                'color' => array('argb' => '000000'),
            ),
        )
    ];
    $sheet->getStyle($range)->applyFromArray($style);

    return $sheet;
}

function setDropdown($sheet, $col, $values)
{
    $validation = $sheet->getCell($col)->getDataValidation();
    $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
    $validation->setFormula1($values);
    $validation->setAllowBlank(false);
    $validation->setShowInputMessage(true);
    $validation->setShowErrorMessage(true);
    $validation->setShowDropDown(true);
    $validation->setErrorTitle('Input error');
    $validation->setError('Value is not in list.');
    $validation->setPromptTitle('Pick from list');
    $validation->setPrompt('Please pick a value from the drop-down list.');
}