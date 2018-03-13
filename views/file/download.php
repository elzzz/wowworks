<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\File */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Files', 'url' => ['download']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="file-download">

    <h1><?= Html::encode($this->title) ?></h1>
    <?= $model->download() ?>
</div>
