<?php

$this->title = Yii::t('hipanel.finance.tariffprofile', 'Create seller profile: {0}', Yii::$app->user->identity->login);
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel.finance.tariffprofile', 'Tariff profiles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<?= $this->render('_form', compact('model', 'client_id', 'client')) ?>
