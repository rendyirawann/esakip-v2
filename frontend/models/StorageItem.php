<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * Registry item EsakipStorage (folder/file) per-SKPD.
 *
 * @property int $id
 * @property int|null $refskpd_id
 * @property int|null $parent_id
 * @property string $name
 * @property string $type            folder | file
 * @property string|null $rel_path
 * @property int|null $size
 * @property string|null $mime
 * @property string|null $local_path
 * @property string|null $nextcloud_path
 * @property string $sync_status     synced | pending | failed
 * @property string|null $sync_message
 * @property int $is_deleted
 * @property int|null $created_by
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class StorageItem extends ActiveRecord
{
    const TYPE_FOLDER = 'folder';
    const TYPE_FILE = 'file';

    const SYNC_SYNCED = 'synced';
    const SYNC_PENDING = 'pending';
    const SYNC_FAILED = 'failed';

    public static function tableName()
    {
        return 'v2_storage_item';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules()
    {
        return [
            [['name', 'type'], 'required'],
            [['refskpd_id', 'parent_id', 'size', 'is_deleted', 'created_by', 'created_at', 'updated_at'], 'integer'],
            [['sync_message'], 'string'],
            [['name', 'mime', 'sync_status'], 'string', 'max' => 255],
            [['type'], 'in', 'range' => [self::TYPE_FOLDER, self::TYPE_FILE]],
            [['rel_path', 'local_path', 'nextcloud_path'], 'string', 'max' => 1000],
            [['sync_status'], 'default', 'value' => self::SYNC_PENDING],
            [['is_deleted'], 'default', 'value' => 0],
        ];
    }

    public function getChildren()
    {
        return $this->hasMany(self::class, ['parent_id' => 'id']);
    }

    public function getParent()
    {
        return $this->hasOne(self::class, ['id' => 'parent_id']);
    }

    public function getSkpd()
    {
        return $this->hasOne(SakipSkpd::class, ['refskpd_id' => 'refskpd_id']);
    }

    public function isFolder()
    {
        return $this->type === self::TYPE_FOLDER;
    }

    /**
     * Rantai folder dari root SKPD hingga item ini (untuk breadcrumb).
     * @return StorageItem[]
     */
    public function ancestors()
    {
        $chain = [];
        $node = $this->parent;
        $guard = 0;
        while ($node !== null && $guard++ < 50) {
            array_unshift($chain, $node);
            $node = $node->parent;
        }
        return $chain;
    }
}
