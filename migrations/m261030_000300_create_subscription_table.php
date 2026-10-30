<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%subscription}}`.
 */
class m261030_000300_create_subscription_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $table = Yii::$app->db->getTableSchema('{{%subscription}}');
        if (!$table) {
            $this->createTable('{{%subscription}}', [
                'id_subscription' => $this->bigPrimaryKey(),
                'id_client' => $this->bigInteger()->notNull(),
                'id_company' => $this->bigInteger()->notNull(),
                'id_paket' => $this->bigInteger()->notNull(),
                'durasi' => $this->integer()->notNull()->comment('Durasi dalam hari'),
                'price' => $this->decimal(12, 2)->notNull()->defaultValue(0),
                'status' => $this->smallInteger()->notNull()->defaultValue(1),
                'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
                'uuid' => 'uuid NULL',
            ]);

            // Indexes and Foreign Keys
            $this->createIndex('idx_subscription_id_client', '{{%subscription}}', 'id_client');
            $this->createIndex('idx_subscription_id_company', '{{%subscription}}', 'id_company');
            $this->createIndex('idx_subscription_id_paket', '{{%subscription}}', 'id_paket');

            $this->addForeignKey(
                'fk_subscription_client',
                '{{%subscription}}',
                'id_client',
                '{{%client}}',
                'id_client',
                'CASCADE',
                'CASCADE'
            );

            $this->addForeignKey(
                'fk_subscription_company',
                '{{%subscription}}',
                'id_company',
                '{{%company}}',
                'id_company',
                'CASCADE',
                'CASCADE'
            );

            $this->addForeignKey(
                'fk_subscription_paket',
                '{{%subscription}}',
                'id_paket',
                '{{%paket}}',
                'id_paket',
                'RESTRICT',
                'CASCADE'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            $this->dropForeignKey('fk_subscription_paket', '{{%subscription}}');
            $this->dropForeignKey('fk_subscription_company', '{{%subscription}}');
            $this->dropForeignKey('fk_subscription_client', '{{%subscription}}');
        } catch (\Exception $e) {
        }

        $this->dropTable('{{%subscription}}');
    }
}
