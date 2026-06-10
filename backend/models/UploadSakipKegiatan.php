<?php

namespace backend\models;

use yii\base\Model;
use yii\web\UploadedFile;

class UploadSakipKegiatan extends Model
{
    /**
     * @var UploadedFile
     */
    public $file;

    public function rules()
    {
        return [
            [['file'], 'file', 'skipOnEmpty' => true, 'maxSize' => 1024 * 1024 * 10], // Ukuran maksimum 10 MB per file
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            $filePath = 'dokumen/csv/' . $this->file->baseName . '.' . $this->file->extension;
            $this->file->saveAs($filePath);
            return $filePath;
        } else {
            return false;
        }
    }
}
