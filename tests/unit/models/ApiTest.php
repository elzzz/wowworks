<?php

namespace tests\unit\models;

use PHPUnit\Framework\TestCase;
use app\modules\api\Api;

class ApiTest extends TestCase
{
    protected $api;

    protected function setUp()
    {
        $this->api = new Api(1);
    }

    public function testControllerNamespace()
    {
        $this->assertEquals('app\modules\api\controllers', $this->api->controllerNamespace);
        $this->assertInternalType('string', $this->api->controllerNamespace);
    }

    public function testInit()
    {
        $this->assertNull($this->api->init());
        $this->api->init();
        $this->assertEquals('app\modules\api\controllers', $this->api->controllerNamespace);
        $this->assertTrue(is_string($this->api->controllerNamespace));
    }
}
