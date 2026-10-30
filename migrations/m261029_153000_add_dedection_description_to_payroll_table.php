<?php

use yii\db\Migration;

/**
 * Handles adding `dedection_description` column to table `{{%payroll}}`.
 */
class m261029_153000_add_dedection_description_to_payroll_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $table = Yii::$app->db->getTableSchema('{{%payroll}}');

        if ($table && !isset($table->columns['dedection_description'])) {
            $this->addColumn('{{%payroll}}', 'dedection_description', $this->string(255)->null());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $table = Yii::$app->db->getTableSchema('{{%payroll}}');

        if ($table && isset($table->columns['dedection_description'])) {
            $this->dropColumn('{{%payroll}}', 'dedection_description');
        }
    }
}
