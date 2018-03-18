<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Url;
use yii\helpers\Html;

/**
 * This is the model class for table "file".
 *
 * @property int $id
 * @property string $name
 * @property string $extension
 * @property int $size
 * @property string $uploaded_at
 * @property string $deleted_at
 *
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
            [['uploaded_at', 'deleted_at'], 'safe'],
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
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'extension' => Yii::t('app', 'Extension'),
            'size' => Yii::t('app', 'Size (MB)'),
            'uploaded_at' => Yii::t('app', 'Uploaded At (UTC)'),
            'deleted_at' => Yii::t('app', 'Should be deleted at (UTC)'),
            'pdfFile' => Yii::t('app', 'PDF File'),
            'image' => Yii::t('app', 'Image'),
            'json' => Yii::t('app', 'Link to JSON'),
        ];
    }

    /**
     * Uploads pdf file for File model.
     * @return mixed
     */
    public function upload()
    {
        if ($this->pdfFile) {
            $path = Url::to('@webroot/upload/pdf/');
            $fileName = strtolower($this->name) . '.pdf';
            $this->pdfFile->saveAs($path . $fileName);

            $pdf = new \Imagick($path . $fileName);
            $pages = $pdf->getNumberImages();
            if ($pages <= 20) {
                Yii::$app->session->setFlash('success', Yii::t('app', "File Uploaded Successfully!"));
                return true;
            } else {
                Yii::$app->session->setFlash('danger', Yii::t('app', "Your PDF has to be less than 20 pages!"));
                unlink($path . $fileName);
                return false;
            }
        }
        return false;
    }

    /**
     * Converts each page of pdf file into images.
     * @return mixed
     */
    public function convert()
    {
        if ($this->pdfFile) {
            $pdfPath = Url::to('@webroot/upload/pdf/');
            $resultPath = Url::to('@webroot/result/');
            $fileName = strtolower($this->name) . '.pdf';

            $pdf = new \Imagick($pdfPath . $fileName);
            $pdf->setImageFormat('jpg');
            mkdir($resultPath . $this->name .'/images/', 0777, true);
            foreach ($pdf as $i => $img) {
                $img->writeImage($resultPath. $this->name .'/images/'.($i+1).'.jpg');
            }
            return true;
        }
        return false;
    }

    /**
     * Gets converted file images.
     * @return array
     */
    public function getImagesCarousel()
    {
        $myDirectory = opendir(Url::to('@webroot/result/') . $this->name .'/images');

        while ($entryName = readdir($myDirectory)) {
            if ($entryName != '.' && $entryName != '..') {
                $dirArray[] = $entryName;
            }
        }

        closedir($myDirectory);

        $indexCount = count($dirArray);
        if ($indexCount >= 1) {
            unset($images);
            foreach ($dirArray as $file) {
                $images[] = '<img src="'.Url::to('/result/') . $this->name . '/images/'.$file.'"/>';
            }
        }
        return $images;
    }

    /**
     * Gets converted file's images path.
     * @return array
     */
    public function getImagesPath()
    {
        $myDirectory = opendir(Url::to('@webroot/result/') . $this->name .'/images');

        while ($entryName = readdir($myDirectory)) {
            if ($entryName != '.' && $entryName != '..') {
                $dirArray[] = $entryName;
            }
        }

        closedir($myDirectory);

        $indexCount = count($dirArray);
        if ($indexCount >= 1) {
            unset($images);
            foreach ($dirArray as $file) {
                $images[] = Url::to('@web/result/') . $this->name . '/images/'.$file;
            }
        }
        return $images;
    }

    /**
     * Gets converted file's images.
     * @return array
     */
    public function getResultImages()
    {
        $myDirectory = opendir(Url::to('@webroot/result/') . $this->name .'/images');

        while ($entryName = readdir($myDirectory)) {
            if ($entryName != '.' && $entryName != '..') {
                $dirArray[] = $entryName;
            }
        }

        closedir($myDirectory);

        $indexCount = count($dirArray);
        if ($indexCount >= 1) {
            unset($images);
            foreach ($dirArray as $file) {
                $images[] = Html::img('images/'.$file);
            }
        }
        return $images;
    }

    /**
     * Copies assets to result folder.
     */
    public function getResultAssets()
    {
        $defaultAssets = Url::to('@webroot/default_assets');
        $assets = Url::to('@webroot/result/') . $this->name . '/assets';

        shell_exec('cp -r '.$defaultAssets.' '.$assets);
    }

    /**
     * Gets result path of the File model.
     * @return mixed
     */
    public function getResultPath()
    {
        $path = Url::to('@webroot/result/') . $this->name . '/index.html';
        return $path;
    }

    /**
     * Sends result archive of the File model.
     * @return mixed
     */
    public function sendFile()
    {
        $file = Url::to('@webroot/result/') . $this->name . '/' . $this->name . '.zip';

        if (file_exists($file)) {
            return Yii::$app->response->sendFile($file);
        }
        return Yii::$app->session->setFlash('warning', Yii::t('app', "File does not exist."));
    }

    /**
     * Creates zip archive of the File model result.
     */
    public function createZip()
    {
        $path = Url::to('@webroot/result/') . $this->name;
        $zipName = $this->name . '.zip';

        shell_exec('cd '.$path.' && zip -r '.$zipName.' .');
    }

    /**
     * Joins functions.
     */
    public function download()
    {
        $this->createZip();
        $this->sendFile();
    }

    /**
     * Gets path to json file.
     */
    public function getJson()
    {
        return Url::to('@web/api/image/json/'.$this->id, true);
    }

    /**
     * @inheritdoc
     */
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
}
