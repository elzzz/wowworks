<?php

namespace app\modules\api;

use yii\base\Module;

/**
 * api module definition class
 */
class Api extends Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\api\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }
}
