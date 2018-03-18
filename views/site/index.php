<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Here is PDF converter!</h1>

        <p class="lead">You can convert your PDF to HTML slider.</p>

        <?= Html::a('Convert', ['file/upload'], ['class' => 'btn btn-primary']) ?>
    </div>
</div>
