<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\db\Expression;
use yii\web\UploadedFile;

/**
 * This is the model class for table "file".
 *
 * @property int $id
 * @property string $name
 * @property string $extension
 * @property int $size
 * @property string $uploaded_at
 */
class File extends ActiveRecord
{
    public $pdf_file;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'file';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'extension', 'size'], 'required'],
            [['size'], 'default', 'value' => null],
            [['size'], 'double'],
            [['uploaded_at'], 'safe'],
            [['name', 'extension'], 'string', 'max' => 255],
            [['pdf_file'], 'file', 'skipOnEmpty' => false, 'extensions' => 'pdf', 'maxSize' => 1024 * 1024 * 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'extension' => 'Extension',
            'size' => 'Size (MB)',
            'uploaded_at' => 'Uploaded At (UTC)',
            'pdf_file' => 'PDF File',
        ];
    }

    public function upload()
    {
        if ($this->pdf_file) {
            $path = Url::to('@webroot/upload/pdf/');
            $filename = strtolower($this->name) . '.pdf';
            $this->pdf_file->saveAs($path . $filename);
        }
        return true;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->upload();
            return true;
        } else {
            return false;
        }
    }
}
