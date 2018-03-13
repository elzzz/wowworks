<?php

use yii\helpers\Html;
use yii\bootstrap\Carousel;

/* @var $this yii\web\View */
/* @var $model app\models\File */

$this->title = $model->name;
$download = $model->download();
$this->params['breadcrumbs'][] = ['label' => 'Files', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="file-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-md-6">
            <?= Carousel::widget(['items'=>$model->getImages(), 'options' => ['data-interval' => 'false']]); ?>
        </div>
        <div class="col-md-6">
            <button onclick="">Download</button>
        </div>
    </div>
</div>
