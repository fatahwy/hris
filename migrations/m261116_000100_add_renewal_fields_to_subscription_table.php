<?php

use app\helpers\DBHelper;
use app\helpers\GeneralHelper;
use yii\db\Migration;

/**
 * Handles adding renewal fields to `{{%subscription}}` and initializing subscription menu/access.
 */
class m261116_000100_add_renewal_fields_to_subscription_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $table = Yii::$app->db->getTableSchema('{{%subscription}}');
        if ($table) {
            if (!$table->getColumn('jumlah_bulan')) {
                $this->addColumn('{{%subscription}}', 'jumlah_bulan', $this->integer()->notNull()->defaultValue(1)->after('durasi'));
            }
            if (!$table->getColumn('approved_by')) {
                $this->addColumn('{{%subscription}}', 'approved_by', $this->bigInteger()->null()->after('status'));
            }
            if (!$table->getColumn('approved_at')) {
                $this->addColumn('{{%subscription}}', 'approved_at', $this->timestamp()->null()->after('approved_by'));
            }
            if (!$table->getColumn('note')) {
                $this->addColumn('{{%subscription}}', 'note', $this->text()->null()->after('approved_at'));
            }
        }

        // Re-init menu and register /trx/subscription/* auth item
        DBHelper::initMenu();

        // Grant /trx/subscription/* to Owner role if it exists
        $ownerExists = (new \yii\db\Query())
            ->from('{{%auth_item}}')
            ->where(['name' => 'Owner', 'type' => 1])
            ->exists();

        if ($ownerExists) {
            $childExists = (new \yii\db\Query())
                ->from('{{%auth_item_child}}')
                ->where(['parent' => 'Owner', 'child' => '/trx/subscription/*'])
                ->exists();

            if (!$childExists) {
                $this->insert('{{%auth_item_child}}', [
                    'parent' => 'Owner',
                    'child' => '/trx/subscription/*',
                ]);
            }
        }

        GeneralHelper::cacheFlush();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%auth_item_child}}', [
            'parent' => 'Owner',
            'child' => '/trx/subscription/*',
        ]);

        $table = Yii::$app->db->getTableSchema('{{%subscription}}');
        if ($table) {
            if ($table->getColumn('note')) {
                $this->dropColumn('{{%subscription}}', 'note');
            }
            if ($table->getColumn('approved_at')) {
                $this->dropColumn('{{%subscription}}', 'approved_at');
            }
            if ($table->getColumn('approved_by')) {
                $this->dropColumn('{{%subscription}}', 'approved_by');
            }
            if ($table->getColumn('jumlah_bulan')) {
                $this->dropColumn('{{%subscription}}', 'jumlah_bulan');
            }
        }
    }
}
