<?php

namespace app\modules\api\controllers;

use Yii;
use yii\web\Response;
use app\modules\api\models\Image;

class ImageController extends \yii\web\Controller
{

    public function actionListImage() {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $image = Image::find()->all();
        if(count($image) > 0) {
            return array('status' => true, 'data' => $image);
        } else {
            return array('status' => false, 'data' => 'No images found.');
        }
    }

}
