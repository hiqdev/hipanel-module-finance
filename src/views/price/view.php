<?php

use yii\helpers\Html;

$this->title = Html::encode($model->id);
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Prices'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

