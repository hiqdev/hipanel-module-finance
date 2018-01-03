<?php

$this->title = Yii::t('hipanel.finance.plan', 'Update plan');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel.finance.plan', 'Plans'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<?= $this->render('_form', compact('models', 'model')) ?>
