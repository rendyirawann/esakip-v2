<?php

namespace frontend\models;

use frontend\controllers\SakipCascadingprogramController;
use Yii;

/**
 * This is the model class for table "simona_keluaranmediacascadingkegiatan".
 *
 * @property int $refsimonakeluaranmediacascadingkegiatan_id
 * @property int|null $refsimonacascadingkegiatan_id
 * @property string|null $file
 * @property string|null $nama_file
 * @property int|null $refuser_id
 * @property int|null $refskpd_id
 */
class SimonaKeluaranmediacascadingkegiatan extends \yii\db\ActiveRecord
{
    public $file_docs; // use a plural name
    public $file_doc;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'simona_keluaranmediacascadingkegiatan';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db1');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refsimonacascadingkegiatan_id', 'refcascadingkegiatan_id', 'refcascadingprogram_id', 'refprogram_id', 'refkegiatan_id', 'refuser_id', 'refskpd_id', 'refsimonarincianbelanjacascadingkegiatan_id'], 'integer'],
            [['nama_file'], 'string'],
            [['file'], 'string', 'max' => 255], // Optional: Add max length for the file name if needed
            [['file_docs'], 'file', 'skipOnEmpty' => true, 'maxFiles' => 3, 'extensions' => 'pdf, doc, docx, jpg, png', 'maxSize' => 1024 * 1024 * 10],  // Validate file extensions and size
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'refsimonakeluaranmediacascadingkegiatan_id' => 'Refsimonakeluaranmediacascadingkegiatan ID',
            'refsimonacascadingkegiatan_id' => 'Refsimonacascadingkegiatan ID',
            'refcascadingkegiatan_id' => 'refcascadingkegiatan_id ID',
            'refcascadingprogram_id' => 'refcascadingprogram_id ID',
            'refprogram_id' => 'refprogram_id ID',
            'refkegiatan_id' => 'refkegiatan_id ID',
            'refsimonarincianbelanjacascadingkegiatan_id' => 'refsimonarincianbelanjacascadingkegiatan ID',
            'file' => 'File',
            'nama_file' => 'Nama File',
            'refuser_id' => 'Refuser ID',
            'refskpd_id' => 'Refskpd ID',
        ];
    }

    public function getRefSimonarincianbelanjacascadingkegiatan()
    {
        return $this->hasOne(SimonaRincianbelanjacascadingkegiatan::class, ['refsimonarincianbelanjacascadingkegiatan_id' => 'refsimonarincianbelanjacascadingkegiatan_id']);
    }

    public function getRefKegiatan()
    {
        return $this->hasOne(SakipKegiatan::class, ['refkegiatan_id' => 'refkegiatan_id']);
    }

    public function getRefSimonaCascadingKegiatan()
    {
        return $this->hasOne(Simonacascadingkegiatan::class, ['refsimonacascadingkegiatan_id' => 'refcascadingkegiatan_id']);
    }

    public function getRefCascadingKegiatan()
    {
        return $this->hasOne(SakipCascadingkegiatan::class, ['refcascadingkegiatan_id' => 'refcascadingkegiatan_id']);
    }

    public function getRefProgram()
    {
        return $this->hasOne(SakipProgram::class, ['refprogram_id' => 'refprogram_id']);
    }

    public function getRefCascadingProgram()
    {
        return $this->hasOne(SakipCascadingprogram::class, ['refcascadingprogram_id' => 'refcascadingprogram_id']);
    }
}
