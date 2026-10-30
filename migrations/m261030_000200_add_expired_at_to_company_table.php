<?php

use yii\db\Migration;

/**
 * Handles adding expired_at to table `{{%company}}`.
 */
class m261030_000200_add_expired_at_to_company_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $table = Yii::$app->db->getTableSchema('{{%company}}');
        if ($table && !isset($table->columns['expired_at'])) {
            $this->addColumn('{{%company}}', 'expired_at', $this->timestamp()->null());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $table = Yii::$app->db->getTableSchema('{{%company}}');
        if ($table && isset($table->columns['expired_at'])) {
            $this->dropColumn('{{%company}}', 'expired_at');
        }
    }
}
