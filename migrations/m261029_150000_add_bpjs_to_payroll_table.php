<?php

use yii\db\Migration;

/**
 * Handles adding BPJS percentage columns to table `{{%payroll}}`.
 */
class m261029_150000_add_bpjs_to_payroll_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $table = Yii::$app->db->getTableSchema('{{%payroll}}');

        if ($table && !isset($table->columns['bpjs_kesehatan'])) {
            $this->addColumn('{{%payroll}}', 'bpjs_kesehatan', $this->decimal(6, 4)->defaultValue(0));
        }

        if ($table && !isset($table->columns['bpjs_jht'])) {
            $this->addColumn('{{%payroll}}', 'bpjs_jht', $this->decimal(6, 4)->defaultValue(0));
        }

        if ($table && !isset($table->columns['bpjs_jp'])) {
            $this->addColumn('{{%payroll}}', 'bpjs_jp', $this->decimal(6, 4)->defaultValue(0));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $table = Yii::$app->db->getTableSchema('{{%payroll}}');

        if ($table && isset($table->columns['bpjs_jp'])) {
            $this->dropColumn('{{%payroll}}', 'bpjs_jp');
        }

        if ($table && isset($table->columns['bpjs_jht'])) {
            $this->dropColumn('{{%payroll}}', 'bpjs_jht');
        }

        if ($table && isset($table->columns['bpjs_kesehatan'])) {
            $this->dropColumn('{{%payroll}}', 'bpjs_kesehatan');
        }
    }
}
