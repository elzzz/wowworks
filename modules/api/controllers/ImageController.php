<?php

namespace app\modules\api\controllers;

use Yii;
use yii\web\Response;
use app\modules\api\models\Image;

class ImageController extends \yii\web\Controller
{

    public function actionJson($id) {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $images = Image::find()->select('url')->where(['file_id'=>$id])->all();
        if(count($images) > 0) {
            return array('images' => $images);
        } else {
            return array('images' => 'No images found');
        }
    }

}
