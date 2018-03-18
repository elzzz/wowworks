<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = Yii::t('app', 'My Yii Application');
?>
<div class="site-index">

    <div class="jumbotron">
        <h1><?= Yii::t('app', 'Here is PDF converter!') ?></h1>

        <p class="lead"><?= Yii::t('app', 'You can convert your PDF to HTML slider.') ?></p>

        <?= Html::a(Yii::t('app', 'Convert'), ['file/upload'], ['class' => 'btn btn-primary']) ?>
    </div>
</div>
