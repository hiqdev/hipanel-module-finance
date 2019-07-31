<?php

$this->title = Yii::t('hipanel', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel.finance.tariffprofile', 'Tariff profiles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<?= $this->render('_form', compact('model', 'client_id', 'client')) ?>
