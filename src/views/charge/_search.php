<?php

/**
 * @var \yii\base\View $this
 * @var \hipanel\widgets\AdvancedSearch $search
 */

use hipanel\modules\client\widgets\combo\ClientCombo;
use hipanel\modules\finance\helpers\CurrencyFilter;
use hipanel\modules\finance\widgets\BillTypeFilter;
use hiqdev\combo\StaticCombo;
use hiqdev\yii2\daterangepicker\DateRangePicker;
use yii\bootstrap\Html;

$currencies = $this->context->getCurrencyTypes();
$currencies = CurrencyFilter::addSymbolAndFilter($currencies);

?>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('ftype')->widget(BillTypeFilter::class) ?>
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
