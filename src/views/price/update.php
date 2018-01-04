<?php

$this->title = Yii::t('hipanel:finance', 'Update plan');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:.inance', 'Plans'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<?= $this->render('_form', compact('models', 'model')) ?>
