<?php

namespace app\models;

use yii\helpers\Url;
use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;
use yii\db\mssql\PDO;

class DeleteFromFile extends BaseObject implements JobInterface {

    public function execute($queue)
    {
        $files = Yii::$app->db->createCommand('SELECT name FROM file WHERE CURRENT_TIMESTAMP > deleted_at')->queryAll(PDO::FETCH_COLUMN);
        $path = Url::to('@webroot/result/');

        foreach ($files as $file) {
//            shell_exec('rm '.$path.$file);
            unlink($path . $file);
        }
    }

}