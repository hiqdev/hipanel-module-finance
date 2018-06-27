<?php

/* @var $this yii\web\View */
/* @var $model \hipanel\modules\finance\forms\CertificateTariffForm */

$this->title = Yii::t('hipanel:finance:tariff', 'Copy certificate tariff');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Tariffs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$model->scenario = 'create-certificate';

?>

<div class="tariff-create">
    <?= $this->render('_form', [
        'model' => $model,
        'action' => ['@tariff/create-certificate']
    ]) ?>
</div>
