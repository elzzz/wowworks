<?php

namespace app\modules\api\controllers;

use app\modules\api\models\Image;
use Yii;
use yii\web\Response;
use yii\web\Controller;

/**
 * ImageController allows to get json file of current File model images.
 */
class ImageController extends Controller
{
    /**
     * Gets json of current File model images.
     * @param integer $id
     * @return mixed
     */
    public function actionJson($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $images = Image::find()->select('url')->where(['file_id'=>$id])->all();
        if (count($images) > 0) {
            return array('images' => $images);
        } else {
            return array('images' => 'No images found');
        }
    }

}
