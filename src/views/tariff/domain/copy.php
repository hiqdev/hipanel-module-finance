<?php

/* @var $this yii\web\View */
/* @var $model \hipanel\modules\finance\forms\DomainTariffForm */

$this->title = Yii::t('hipanel:finance:tariff', 'Create domain tariff');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Tariffs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="tariff-create">
    <?= $this->render('_form', [
        'model' => $model,
        'action' => ['@tariff/create-domain', 'parent_id' => $model->parentTariff->id]
    ]); ?>
</div>
