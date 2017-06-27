<?php

/* @var $this yii\web\View */
/* @var $model \hipanel\modules\finance\forms\ServerTariffForm */

$this->title = Yii::t('hipanel:finance:tariff', 'Update server tariff');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Tariffs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="tariff-update">
    <?= $this->render('_form', ['model' => $model]); ?>
</div>
