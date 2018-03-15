<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Carousel;

/* @var $this yii\web\View */
/* @var $model app\models\File */

$this->title = $model->name;
?>
<div class="file-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-md-6">
            <?= Carousel::widget(['items'=>$model->getImagesCarousel(), 'options' => ['data-interval' => 'false']]); ?>
        </div>
        <div class="col-md-6">
            <a href="<?= Url::to(['file/download', 'id' => $model->id]) ?>"><button class="btn btn-primary">Download</button></a>
        </div>
    </div>
</div>
