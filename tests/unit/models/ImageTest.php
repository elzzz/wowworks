<?php

namespace tests\unit\models;

use PHPUnit\Framework\TestCase;
use app\models\File;
use app\modules\api\models\Image;

class ImageTest extends TestCase
{
    protected $image;

    protected function setUp()
    {
        $this->image = new Image();
    }

    public function testTableName()
    {
        $this->assertEquals('image', $this->image->tableName());
    }

    public function testAttributeLabels()
    {
        $this->assertEquals(
            array(
                'id' => 'ID',
                'file_id' => 'File ID',
                'url' => 'URL',
                'number' => 'Number of the page',
                'path' => 'Path to file'),
            $this->image->attributeLabels()
        );
    }

    public function testRules()
    {
        $this->assertEquals(
            array(
                [['file_id', 'number'], 'required'],
                [['file_id'], 'default', 'value' => null],
                [['file_id', 'number'], 'integer'],
                [['url', 'path'], 'string'],
                [['file_id'], 'exist', 'skipOnError' => true, 'targetClass' => File::className(),
                    'targetAttribute' => ['file_id' => 'id']]),
            $this->image->rules()
        );
    }

    public function testGetFile()
    {
        $this->assertEquals($this->image->hasOne(File::className(), ['id' => 'file_id']), $this->image->getFile());
    }

//    ERROR: Call to a member function getDb() on null
//    public function testValidSave()
//    {
//        $this->image->setAttributes(array(
//            'path' => 'somepath',
//            'file_id' => 1,
//            'number' => 1,
//            'url' => 'someurl',
//        ));
//
//        $this->assertTrue($this->image->save());
//    }
}
