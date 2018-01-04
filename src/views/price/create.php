<?php

$this->title = Yii::t('hipanel:finance', 'Create plan');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Plans'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<?= $this->render('_form', compact('models', 'model')) ?>
