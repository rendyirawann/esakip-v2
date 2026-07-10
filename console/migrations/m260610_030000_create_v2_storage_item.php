<?php

use yii\db\Migration;

/**
 * Tabel registry EsakipStorage: pohon folder/file per-SKPD + status sinkron NextCloud.
 */
class m260610_030000_create_v2_storage_item extends Migration
{
    public function safeUp()
    {
        $this->createTable('v2_storage_item', [
            'id' => $this->primaryKey(),
            'refskpd_id' => $this->integer()->null()->comment('SKPD pemilik item'),
            'parent_id' => $this->integer()->null()->comment('Folder induk (null = root SKPD)'),
            'name' => $this->string(255)->notNull(),
            'type' => $this->string(10)->notNull()->defaultValue('folder')->comment('folder | file'),
            'rel_path' => $this->string(1000)->null()->comment('Path relatif di bawah folder SKPD'),
            'size' => $this->bigInteger()->null(),
            'mime' => $this->string(150)->null(),
            'local_path' => $this->string(1000)->null()->comment('Path file di storage server'),
            'nextcloud_path' => $this->string(1000)->null()->comment('Path relatif di NextCloud (di bawah baseFolder)'),
            'sync_status' => $this->string(20)->notNull()->defaultValue('pending')->comment('synced | pending | failed'),
            'sync_message' => $this->text()->null(),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
            'created_by' => $this->integer()->null(),
            'created_at' => $this->integer()->null(),
            'updated_at' => $this->integer()->null(),
        ]);

        $this->createIndex('idx-storage-skpd-parent', 'v2_storage_item', ['refskpd_id', 'parent_id']);
        $this->createIndex('idx-storage-parent', 'v2_storage_item', 'parent_id');
        $this->createIndex('idx-storage-deleted', 'v2_storage_item', 'is_deleted');
    }

    public function safeDown()
    {
        $this->dropTable('v2_storage_item');
    }
}
