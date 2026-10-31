<?php

use yii\db\Migration;

/**
 * Handles adding payroll_cutoff_date to table `{{%company}}`.
 */
class m261031_130000_add_payroll_cutoff_date_to_company_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $table = Yii::$app->db->getTableSchema('{{%company}}');
        if ($table && !isset($table->columns['payroll_cutoff_date'])) {
            $this->addColumn('{{%company}}', 'payroll_cutoff_date', $this->integer()->defaultValue(20)->notNull()->comment('Tanggal Cut Off Payroll'));
            $this->update('{{%company}}', ['payroll_cutoff_date' => 20], ['payroll_cutoff_date' => null]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $table = Yii::$app->db->getTableSchema('{{%company}}');
        if ($table && isset($table->columns['payroll_cutoff_date'])) {
            $this->dropColumn('{{%company}}', 'payroll_cutoff_date');
        }
    }
}
