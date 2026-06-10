<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "simona_mediacascadingsubkegiatan_opd".
 *
 * @property int $refsimonamediacascadingsubkegiatanopd_id
 * @property int|null $refsimonacascadingsubkegiatan_id
 * @property string|null $file
 * @property string|null $nama_file
 * @property int|null $refuser_id
 * @property int|null $refskpd_id
 */
class SimonaMediacascadingsubkegiatanOpd extends \yii\db\ActiveRecord
{
    public $file_docs;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'simona_mediacascadingsubkegiatan_opd';
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
            [['refsimonacascadingsubkegiatan_id', 'refuser_id', 'refskpd_id'], 'integer'],
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
            'refsimonamediacascadingsubkegiatanopd_id' => 'Refsimonamediacascadingsubkegiatanopd ID',
            'refsimonacascadingsubkegiatan_id' => 'Refsimonacascadingsubkegiatan ID',
            'file' => 'File',
            'nama_file' => 'Nama File',
            'refuser_id' => 'Refuser ID',
            'refskpd_id' => 'Refskpd ID',
        ];
    }
}
