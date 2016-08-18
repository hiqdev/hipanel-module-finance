<?php
/* @var $this yii\web\View */
/* @var $model \hipanel\modules\finance\forms\DomainTariffForm */

$this->title = Yii::t('hipanel/finance/tariff', 'Update domain tariff');
$this->breadcrumbs->setItems([['label' => Yii::t('hipanel/finance', 'Tariffs'), 'url' => ['index']]]);
$this->breadcrumbs->setItems([['label' => $model->name, 'url' => ['view', 'id' => $model->id]]]);
$this->breadcrumbs->setItems([$this->title]);
?>

<div class="tariff-create">
    <?= $this->render('_form', ['model' => $model]); ?>
</div>
