<?php

use yii\db\Migration;

/**
 * Handles adding referensi to table `{{%client}}`.
 */
class m261030_000400_add_referensi_to_client_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $table = Yii::$app->db->getTableSchema('{{%client}}');
        if ($table && !isset($table->columns['referensi'])) {
            $this->addColumn('{{%client}}', 'referensi', $this->string(255)->null());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $table = Yii::$app->db->getTableSchema('{{%client}}');
        if ($table && isset($table->columns['referensi'])) {
            $this->dropColumn('{{%client}}', 'referensi');
        }
    }
}
