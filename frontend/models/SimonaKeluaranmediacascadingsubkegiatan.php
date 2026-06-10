<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "simona_keluaranmediacascadingsubkegiatan".
 *
 * @property int $refsimonakeluaranmediacascadingsubkegiatan_id
 * @property int|null $refsimonacascadingsubkegiatan_id
 * @property int|null $refsimonarincianbelanjacascadingkegiatan_id
 * @property string|null $file
 * @property string|null $nama_file
 * @property int|null $refuser_id
 * @property int|null $refskpd_id
 */
class SimonaKeluaranmediacascadingsubkegiatan extends \yii\db\ActiveRecord
{
    public $file_docs; // use a plural name
    public $file_doc;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'simona_keluaranmediacascadingsubkegiatan';
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
            [['refsimonacascadingsubkegiatan_id', 'refsimonarincianbelanjacascadingsubkegiatan_id', 'refuser_id', 'refskpd_id', 'refcascadingprogram_id', 'refcacadingkegiatan_id', 'refcascadingsubkegiatan_id', 'refprogram_id', 'refkegiatan_id', 'refsubkegiatan_id'], 'integer'],
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
            'refsimonakeluaranmediacascadingsubkegiatan_id' => 'Refsimonakeluaranmediacascadingsubkegiatan ID',
            'refsimonacascadingsubkegiatan_id' => 'Refsimonacascadingsubkegiatan ID',
            'refsimonarincianbelanjacascadingsubkegiatan_id' => 'refsimonarincianbelanjacascadingsubkegiatan_id ID',
            'refcascadingprogram_id' => 'refcascadingprogram_id ID',
            'refcascadingkegiatan_id' => 'refcascadingkegiatan_id ID',
            'refcascadingsubkegiatan_id' => 'refcascadingsubkegiatan_id ID',
            'refprogram_id' => 'refprogram_id ID',
            'refkegiatan_id' => 'refkegiatan_id ID',
            'refsubkegiatan_id' => 'refsubkegiatan_id ID',
            'file' => 'File',
            'nama_file' => 'Nama File',
            'refuser_id' => 'Refuser ID',
            'refskpd_id' => 'Refskpd ID',
        ];
    }
}
