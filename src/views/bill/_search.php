<?php

use hipanel\modules\client\models\ClientSearch;
use hipanel\modules\client\widgets\combo\ClientCombo;
use hipanel\modules\client\widgets\combo\SellerCombo;
use hipanel\modules\finance\helpers\CurrencyFilter;
use hipanel\modules\finance\widgets\BillTypeVueTreeSelect;
use hipanel\modules\finance\widgets\combo\BillRequisitesCombo;
use hipanel\modules\finance\widgets\combo\PlanCombo;
use hipanel\modules\finance\widgets\TreeSelectBehavior;
use hipanel\widgets\AdvancedSearch;
use hipanel\widgets\TagsInput;
use hiqdev\combo\StaticCombo;
use hiqdev\yii2\daterangepicker\DateRangePicker;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var $this View
 * @var AdvancedSearch $search
 * @var array $billTypesList
 * @var array $clientTypes
 */
?>

<?php if (Yii::$app->user->can('support')) : ?>
    <div class="col-md-4 col-sm-6 col-xs-12">
        <?= $search->field('client_id')->widget(ClientCombo::class) ?>
    </div>
<?php endif ?>

<?php if (Yii::$app->user->can('requisites.read')) : ?>
    <?= Html::tag('div', $search->field('requisite_id')->widget(BillRequisitesCombo::class), ['class' => 'col-md-4 col-sm-6 col-xs-12']) ?>
<?php endif ?>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?php
    $currencies = $this->context->getCurrencyTypes();
    $currencies = CurrencyFilter::addSymbolAndFilter($currencies);

    echo $search->field('currency_in')->widget(StaticCombo::class, [
        'data' => $currencies,
        'hasId' => true,
        'multiple' => true,
    ]) ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('type_ids')->widget(BillTypeVueTreeSelect::class, [
        'billTypes' => $billTypesList,
        'multiple' => true,
        'deprecatedTypes' => Yii::$app->params['module.finance.bill.types']['deprecated.types'],
        'behavior' => TreeSelectBehavior::Deprecated,
    ]) ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <div class="form-group">
        <?= DateRangePicker::widget([
            'model' => $search->model,
            'attribute' => 'time_from',
            'attribute2' => 'time_till',
            'options' => [
                'class' => 'form-control',
                'placeholder' => Yii::t('hipanel', 'Date'),
            ],
            'clientOptions' => [
                'showDropdowns' => true,
            ],
            'dateFormat' => 'yyyy-MM-dd',
        ]) ?>
    </div>
</div>


<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('servers') ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('descr') ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('object_name_ilike') ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('tariff_id')->widget(PlanCombo::class) ?>
</div>

<?php if (Yii::$app->user->can('support')) : ?>
    <div class="col-md-4 col-sm-6 col-xs-12">
        <?= $search->field('seller_id')->widget(SellerCombo::class) ?>
    </div>
<?php endif ?>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('object_types')->widget(StaticCombo::class, [
        'data' => $search->model->getObjectTypes(),
        'hasId' => false,
        'multiple' => true,
    ]) ?>
</div>

<?php if (Yii::$app->user->can('client.update')) : ?>
  <div class="col-md-4 col-sm-6 col-xs-12">
      <?= $search->field('client_tags')->widget(TagsInput::class, [
        'searchModel' => new ClientSearch(),
      ]) ?>
  </div>
<?php endif ?>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('client_types')->widget(StaticCombo::class, [
      'data' => $clientTypes,
      'hasId' => true,
      'multiple' => true,
    ]) ?>
</div>
