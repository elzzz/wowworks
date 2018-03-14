<?php
ob_start();

/* @var $this yii\web\View */
/* @var $model app\models\File */
/* @var $content string */

use yii\bootstrap\Carousel;
use yii\helpers\Html;

?>

<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <link>
    <title><?= Html::encode($this->title) ?></title>
    <link href="assets/css/bootstrap.css" rel="stylesheet">
</head>
    <div class="container">
        <div class="file-download">
            <?= Carousel::widget(['items'=>$model->getResultImages(), 'options' => ['data-interval' => 'false']]); ?>
        </div>
    </div>

<script src="assets/js/jquery.js"></script>
<script src="assets/js/bootstrap.js"></script>
</body>
</html>

<?php

file_put_contents($model->getResultPath(), ob_get_contents());
$model->getResultAssets();
$model->download();
?>

