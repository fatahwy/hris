<?php

use yii\db\Migration;

/**
 * Handles adding employee fields to table `{{%user}}`.
 */
class m261028_074557_add_employee_fields_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $table = Yii::$app->db->getTableSchema('{{%user}}');

        $columns = [
            // Tab 1: Data Pribadi - Identitas
            'nik' => $this->string(30)->null(),
            'gender' => $this->string(20)->null(),
            'birth_place' => $this->string(100)->null(),
            'birth_date' => $this->date()->null(),
            'marital_status' => $this->string(30)->null(),
            'religion' => $this->string(30)->null(),
            'education' => $this->string(50)->null(),
            'photo' => $this->string(255)->null(),

            // Tab 1: Data Pribadi - Alamat
            'ktp_address' => $this->text()->null(),
            'is_domicile_same_ktp' => $this->smallInteger()->defaultValue(1),
            'domicile_address' => $this->text()->null(),

            // Tab 1: Data Pribadi - Kontak Darurat
            'emergency_contact_name' => $this->string(100)->null(),
            'emergency_contact_relation' => $this->string(50)->null(),
            'emergency_contact_phone' => $this->string(50)->null(),

            // Tab 2: Pekerjaan - Informasi Pekerjaan
            'employment_status' => $this->string(50)->null(),
            'id_manager' => $this->bigInteger()->null(),
            'work_location' => $this->string(100)->null(),
            'contract_end_date' => $this->date()->null(),
            'leave_entitlement_date' => $this->date()->null(),

            // Tab 2: Pekerjaan - Pengunduran Diri
            'resign_date' => $this->date()->null(),
            'resign_reason' => $this->string(100)->null(),

            // Tab 2: Pekerjaan - Jatah Cuti
            'annual_leave_quota' => $this->integer()->defaultValue(12),
            'remaining_leave' => $this->integer()->defaultValue(12),

            // Tab 3: Payroll & Pajak
            'npwp' => $this->string(50)->null(),
            'tax_method' => $this->string(30)->defaultValue('Gross'),
            'bank_name' => $this->string(50)->null(),
            'bank_account_number' => $this->string(50)->null(),
            'bank_account_holder' => $this->string(100)->null(),

            // Tab 4: BPJS - Nomor Kepesertaan
            'bpjs_kesehatan_number' => $this->string(50)->null(),
            'bpjs_ketenagakerjaan_number' => $this->string(50)->null(),

            // Tab 4: BPJS - Program yang Diikuti
            'bpjs_kesehatan' => $this->smallInteger()->defaultValue(1),
            'bpjs_jht' => $this->smallInteger()->defaultValue(1),
            'bpjs_jp' => $this->smallInteger()->defaultValue(1),
            'bpjs_jkk' => $this->smallInteger()->defaultValue(1),
            'bpjs_jkm' => $this->smallInteger()->defaultValue(1),
        ];

        foreach ($columns as $name => $type) {
            if ($table && !isset($table->columns[$name])) {
                $this->addColumn('{{%user}}', $name, $type);
            }
        }

        // Add foreign key for manager
        try {
            $this->createIndex('idx_user_id_manager', '{{%user}}', 'id_manager');
            $this->addForeignKey(
                'fk_user_manager',
                '{{%user}}',
                'id_manager',
                '{{%user}}',
                'id_user',
                'SET NULL',
                'CASCADE'
            );
        } catch (\Exception $e) {
            // Index or FK may already exist
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            $this->dropForeignKey('fk_user_manager', '{{%user}}');
        } catch (\Exception $e) {}

        try {
            $this->dropIndex('idx_user_id_manager', '{{%user}}');
        } catch (\Exception $e) {}

        $columns = [
            'bpjs_jkm',
            'bpjs_jkk',
            'bpjs_jp',
            'bpjs_jht',
            'bpjs_kesehatan',
            'bpjs_ketenagakerjaan_number',
            'bpjs_kesehatan_number',
            'bank_account_holder',
            'bank_account_number',
            'bank_name',
            'tax_method',
            'npwp',
            'remaining_leave',
            'annual_leave_quota',
            'resign_reason',
            'resign_date',
            'leave_entitlement_date',
            'contract_end_date',
            'work_location',
            'id_manager',
            'employment_status',
            'emergency_contact_phone',
            'emergency_contact_relation',
            'emergency_contact_name',
            'domicile_address',
            'is_domicile_same_ktp',
            'ktp_address',
            'photo',
            'education',
            'religion',
            'marital_status',
            'birth_date',
            'birth_place',
            'gender',
            'nik',
        ];

        $table = Yii::$app->db->getTableSchema('{{%user}}');
        foreach ($columns as $column) {
            if ($table && isset($table->columns[$column])) {
                $this->dropColumn('{{%user}}', $column);
            }
        }
    }
}
