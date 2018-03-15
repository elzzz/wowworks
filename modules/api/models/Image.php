<?php

namespace app\modules\api\models;

use Yii;
use app\models\File;

/**
 * This is the model class for table "image".
 *
 * @property int $id
 * @property int $file_id
 * @property string $url
 * @property int $number
 * @property string $path
 *
 * @property File $file
 */
class Image extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'image';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['file_id', 'number'], 'required'],
            [['file_id'], 'default', 'value' => null],
            [['file_id', 'number'], 'integer'],
            [['url', 'path'], 'string'],
            [['file_id'], 'exist', 'skipOnError' => true, 'targetClass' => File::className(), 'targetAttribute' => ['file_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'file_id' => 'File ID',
            'url' => 'URL',
            'number' => 'Number of the page',
            'path' => 'Path to file'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFile()
    {
        return $this->hasOne(File::className(), ['id' => 'file_id']);
    }
}
