<?php

use yii\bootstrap\Carousel;
use yii\helpers\Html;
use yii\helpers\Url;

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
            <?= Html::a('Download', ['file/download', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
</div>
