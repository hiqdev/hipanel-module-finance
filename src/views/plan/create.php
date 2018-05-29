<?php

$this->title = Yii::t('hipanel', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Tariff plans'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<?= $this->render('_form', compact('models', 'model')) ?>
