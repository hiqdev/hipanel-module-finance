<?php

use hipanel\modules\client\widgets\combo\ClientCombo;
use hipanel\modules\client\widgets\combo\SellerCombo;
use hipanel\modules\finance\helpers\CurrencyFilter;
use hipanel\modules\finance\widgets\BillTypeVueTreeSelect;
use hipanel\modules\finance\widgets\combo\PlanCombo;
use hipanel\modules\stock\widgets\combo\OrderCombo;
use hipanel\widgets\AdvancedSearch;
use hiqdev\combo\StaticCombo;
use hiqdev\yii2\daterangepicker\DateRangePicker;
use yii\base\View;

/**
 * @var View $this
 * @var AdvancedSearch $search
 * @var array $clientTypes
 * @var array $billTypesList
 */

$currencies = $this->context->getCurrencyTypes();
$currencies = CurrencyFilter::addSymbolAndFilter($currencies);

?>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field( "type_ids")->widget(BillTypeVueTreeSelect::class, [
        'billTypes' => $billTypesList,
        'multiple' => true,
        'deprecatedTypes' => Yii::$app->params['module.finance.bill.types']['deprecated.types'],
        'behavior' => BillTypeVueTreeSelect::BEHAVIOR_DEPRECATED,
    ]) ?>
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
    <?= $search->field('client_type_in')->widget(StaticCombo::class, [
        'hasId' => true,
        'data' => $clientTypes,
        'multiple' => true,
    ]) ?>
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
        <?= DateRangePicker::widget([
            'model' => $search->model,
            'attribute' => 'time_from',
            'attribute2' => 'time_till',
            'options' => [
                'class' => 'form-control',
                'placeholder' => Yii::t('hipanel', 'Time'),
            ],
            'dateFormat' => 'yyyy-MM-dd',
        ]) ?>
    </div>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('sum_not_zero', ['options' => ['class' => 'form-group checkbox']])->checkbox(['class' => 'option-input']) ?>
</div>

<?php if (Yii::$app->user->can('bill.create')) : ?>
    <div class="col-md-4 col-sm-6 col-xs-12">
        <?= $search->field('hide_internal_clients', ['options' => ['class' => 'form-group checkbox']])->checkbox(['class' => 'option-input']) ?>
    </div>
<?php endif ?>
