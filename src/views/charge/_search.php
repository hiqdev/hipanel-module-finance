<?php

/**
 * @var \yii\base\View $this
 * @var \hipanel\widgets\AdvancedSearch $search
 * @var array $billTypes
 * @var array $billGroupLabels
 */

use hipanel\modules\client\widgets\combo\ClientCombo;
use hipanel\modules\client\widgets\combo\SellerCombo;
use hipanel\modules\finance\helpers\CurrencyFilter;
use hipanel\modules\finance\widgets\combo\MultipleBillTypeCombo;
use hipanel\modules\finance\widgets\combo\PlanCombo;
use hipanel\modules\stock\widgets\combo\OrderCombo;
use hiqdev\combo\StaticCombo;
use hiqdev\yii2\daterangepicker\DateRangePicker;
use yii\bootstrap\Html;

$currencies = $this->context->getCurrencyTypes();
$currencies = CurrencyFilter::addSymbolAndFilter($currencies);

?>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= Html::tag('label', Yii::t('hipanel', 'Type'), ['class' => 'control-label']); ?>
    <?= $search->field('type_in')->widget(MultipleBillTypeCombo::class, compact('billTypes', 'billGroupLabels')) ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('currency_in')->widget(StaticCombo::class, [
        'data' => $currencies,
        'hasId' => true,
        'multiple' => true,
    ]) ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('name_ilike') ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('object_id') ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('label_ilike') ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('client_id')->widget(ClientCombo::class) ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('seller_id')->widget(SellerCombo::class) ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('tariff_id')->widget(PlanCombo::class) ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('order_id')->widget(OrderCombo::class) ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <div class="form-group">
        <?= Html::tag('label', Yii::t('hipanel', 'Time'), ['class' => 'control-label']); ?>
        <?= DateRangePicker::widget([
            'model' => $search->model,
            'attribute' => 'time_from',
            'attribute2' => 'time_till',
            'options' => [
                'class' => 'form-control',
            ],
            'dateFormat' => 'yyyy-MM-dd',
        ]) ?>
    </div>
</div>
