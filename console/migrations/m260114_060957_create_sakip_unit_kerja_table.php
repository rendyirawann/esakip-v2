<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%sakip_unit_kerja}}` (Second step or index adjustments).
 */
class m260114_060957_create_sakip_unit_kerja_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Table already created in previous migration m260114_055144.
        echo "m260114_060957 safeUp: Table sakip_unit_kerja already handled.\n";
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m260114_060957 safeDown: Nothing to revert.\n";
        return true;
    }
}
