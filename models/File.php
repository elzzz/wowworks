<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Url;


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

            $pdf = new \Imagick($path . $filename);
            $pages = $pdf->getNumberImages();
            if ($pages <= 20) {
                Yii::$app->session->setFlash('success', "File Uploaded Successfully!");
                return true;
            } else {
                Yii::$app->session->setFlash('danger', "Your PDF has to be less than 20 pages!");
                unlink($path . $filename);
                return false;
            }
        }
        return false;
    }

    public function convert()
    {
        if ($this->pdf_file) {
            $pdf_path = Url::to('@webroot/upload/pdf/');
            $result_path = Url::to('@webroot/result/');
            $filename = strtolower($this->name) . '.pdf';

            $pdf = new \Imagick($pdf_path . $filename);
            $pdf->setImageFormat('jpg');
            mkdir($result_path . $this->name .'/images/', 0777, true);
            foreach($pdf as $i=>$img) {
                $img->writeImage($result_path. $this->name .'/images/'.($i+1).'.jpg');
            }
            return true;
        }
        return false;
    }

    public function getImages() {
        $myDirectory = opendir(Url::to('@webroot/result/') . $this->name .'/images');

        while($entryName = readdir($myDirectory)) {
            if ($entryName != '.' && $entryName != '..') {
                $dirArray[] = $entryName;
            }
        }

        closedir($myDirectory);

        $indexCount = count($dirArray);
        if ($indexCount >= 1){
            unset($images);
            foreach ($dirArray as $file){
                $images[] = '<img src="'.Url::to('/result/') . $this->name . '/images/'.$file.'"/>';
            }
        }
        return $images;
    }

    public function sendFile() {
        $file = Url::to('@webroot/upload/pdf/') . $this->name . '.pdf';

        if (file_exists($file)) {
            return Yii::$app->response->xSendFile($file);
        }
        Yii::$app->session->setFlash('warning', "File does not exist.");
    }

    public function createZip(){
        $path = Url::to('@webroot/result/') . $this->name;
        $zip_name = $this->name . '.zip';

        shell_exec('cd '.$path.' && zip -r '.$zip_name.' .');
    }

    public function download() {
        $this->createZip();
        $this->sendFile();
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->upload() and $this->convert()) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
    }
}
