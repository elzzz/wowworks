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
    public $pdfFile;

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
            [['pdfFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'pdf', 'maxSize' => 1024 * 1024 * 50],
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
            'pdfFile' => 'PDF File',
        ];
    }

    public function upload()
    {
        if ($this->pdfFile) {
            $path = Url::to('@webroot/upload/pdf/');
            $fileName = strtolower($this->name) . '.pdf';
            $this->pdfFile->saveAs($path . $fileName);

            $pdf = new \Imagick($path . $fileName);
            $pages = $pdf->getNumberImages();
            if ($pages <= 20) {
                Yii::$app->session->setFlash('success', "File Uploaded Successfully!");
                return true;
            } else {
                Yii::$app->session->setFlash('danger', "Your PDF has to be less than 20 pages!");
                unlink($path . $fileName);
                return false;
            }
        }
        return false;
    }

    public function convert()
    {
        if ($this->pdfFile) {
            $pdfPath = Url::to('@webroot/upload/pdf/');
            $resultPath = Url::to('@webroot/result/');
            $fileName = strtolower($this->name) . '.pdf';

            $pdf = new \Imagick($pdfPath . $fileName);
            $pdf->setImageFormat('jpg');
            mkdir($resultPath . $this->name .'/images/', 0777, true);
            foreach($pdf as $i=>$img) {
                $img->writeImage($resultPath. $this->name .'/images/'.($i+1).'.jpg');
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

    public function getResultImages() {
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
                $images[] = '<img src="images/'.$file.'"/>';
            }
        }
        return $images;
    }

    public function getResultAssets() {
        $defaultAssets = Url::to('@webroot/default_assets');
        $assets = Url::to('@webroot/result/') . $this->name . '/assets';

        shell_exec('cp -r '.$defaultAssets.' '.$assets);
    }

    public function getResultPath() {
        $path = Url::to('@webroot/result/') . $this->name . '/index.html';
        return $path;
    }

    public function sendFile() {
        $file = Url::to('@webroot/result/') . $this->name . '/' . $this->name . '.zip';

        if (file_exists($file)) {
            return Yii::$app->response->sendFile($file);
        }
        Yii::$app->session->setFlash('warning', "File does not exist.");
    }

    public function createZip(){
        $path = Url::to('@webroot/result/') . $this->name;
        $zipName = $this->name . '.zip';

        shell_exec('cd '.$path.' && zip -r '.$zipName.' .');
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
