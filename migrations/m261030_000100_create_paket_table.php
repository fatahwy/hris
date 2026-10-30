<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%paket}}`.
 */
class m261030_000100_create_paket_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $table = Yii::$app->db->getTableSchema('{{%paket}}');
        if (!$table) {
            $this->createTable('{{%paket}}', [
                'id_paket' => $this->bigPrimaryKey(),
                'nama_paket' => $this->string(100)->notNull(),
                'kode' => $this->string(50)->notNull()->unique(),
                'tier' => $this->string(50)->notNull(),
                'max_user' => $this->integer()->notNull(),
                'durasi_hari' => $this->integer()->notNull(),
                'durasi_satuan' => $this->string(20)->notNull(),
                'price' => $this->decimal(12, 2)->notNull()->defaultValue(0),
                'is_active_immediately' => $this->smallInteger()->notNull()->defaultValue(0),
                'status' => $this->smallInteger()->notNull()->defaultValue(1),
                'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'uuid' => 'uuid NULL',
            ]);

            // Create view master_paket for backward compatibility or alternate naming
            try {
                $this->execute('CREATE OR REPLACE VIEW master_paket AS SELECT * FROM paket');
            } catch (\Exception $e) {
                // Ignore if view cannot be created
            }

            // Seed master paket data
            $now = date('Y-m-d H:i:s');
            $packages = [
                [
                    'nama_paket' => 'Trial (14 Hari)',
                    'kode' => 'TRIAL',
                    'tier' => 'Trial',
                    'max_user' => 3,
                    'durasi_hari' => 14,
                    'durasi_satuan' => 'hari',
                    'price' => 0,
                    'is_active_immediately' => 1,
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'nama_paket' => 'Mikro (1 Bulan)',
                    'kode' => 'MIKRO_1B',
                    'tier' => 'Mikro',
                    'max_user' => 10,
                    'durasi_hari' => 30,
                    'durasi_satuan' => 'bulan',
                    'price' => 199000,
                    'is_active_immediately' => 0,
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'nama_paket' => 'Mikro (1 Tahun)',
                    'kode' => 'MIKRO_1T',
                    'tier' => 'Mikro',
                    'max_user' => 10,
                    'durasi_hari' => 365,
                    'durasi_satuan' => 'tahun',
                    'price' => 1990000,
                    'is_active_immediately' => 0,
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'nama_paket' => 'Kecil (1 Bulan)',
                    'kode' => 'KECIL_1B',
                    'tier' => 'Kecil',
                    'max_user' => 25,
                    'durasi_hari' => 30,
                    'durasi_satuan' => 'bulan',
                    'price' => 399000,
                    'is_active_immediately' => 0,
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'nama_paket' => 'Kecil (1 Tahun)',
                    'kode' => 'KECIL_1T',
                    'tier' => 'Kecil',
                    'max_user' => 25,
                    'durasi_hari' => 365,
                    'durasi_satuan' => 'tahun',
                    'price' => 3990000,
                    'is_active_immediately' => 0,
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'nama_paket' => 'Menengah (1 Bulan)',
                    'kode' => 'MENENGAH_1B',
                    'tier' => 'Menengah',
                    'max_user' => 50,
                    'durasi_hari' => 30,
                    'durasi_satuan' => 'bulan',
                    'price' => 649000,
                    'is_active_immediately' => 0,
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'nama_paket' => 'Menengah (1 Tahun)',
                    'kode' => 'MENENGAH_1T',
                    'tier' => 'Menengah',
                    'max_user' => 50,
                    'durasi_hari' => 365,
                    'durasi_satuan' => 'tahun',
                    'price' => 6490000,
                    'is_active_immediately' => 0,
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'nama_paket' => 'Bisnis (1 Bulan)',
                    'kode' => 'BISNIS_1B',
                    'tier' => 'Bisnis',
                    'max_user' => 100,
                    'durasi_hari' => 30,
                    'durasi_satuan' => 'bulan',
                    'price' => 999000,
                    'is_active_immediately' => 0,
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'nama_paket' => 'Bisnis (1 Tahun)',
                    'kode' => 'BISNIS_1T',
                    'tier' => 'Bisnis',
                    'max_user' => 100,
                    'durasi_hari' => 365,
                    'durasi_satuan' => 'tahun',
                    'price' => 9990000,
                    'is_active_immediately' => 0,
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ];

            foreach ($packages as $pkg) {
                $this->insert('{{%paket}}', $pkg);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            $this->execute('DROP VIEW IF EXISTS master_paket');
        } catch (\Exception $e) {
        }

        $this->dropTable('{{%paket}}');
    }
}
