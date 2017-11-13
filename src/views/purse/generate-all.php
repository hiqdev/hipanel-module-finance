<?php

$this->title = Yii::t('hipanel:finance', 'Generate documents');
$this->params['subtitle'] = $this->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:document', 'Documents'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<h2><?= $this->title ?></h2>
