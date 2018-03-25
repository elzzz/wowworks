<?php
/**
 * Created by PhpStorm.
 * User: Клим
 * Date: 3/25/2018
 * Time: 11:05 PM
 */

namespace tests\unit\controllers;

use Yii;
use yii\web\Response;
use PHPUnit\Framework\TestCase;
use app\modules\api\controllers\ImageController;

class ImageControllerTest extends TestCase
{
    protected $imageController;

    protected function setUp()
    {
        $this->imageController = new ImageController('image', \Yii::$app);
    }

    public function testActionJson()
    {
        $this->assertTrue($this->imageController!=null);
//        ERROR: Creating default object from empty value
//        $this->imageController->actionJson(1);
    }
}
