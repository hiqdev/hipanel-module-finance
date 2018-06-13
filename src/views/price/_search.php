<?php

/** @var \hipanel\widgets\AdvancedSearch $search */

use hipanel\helpers\StringHelper;

$this->registerCss('#form-advancedsearch-price-search .hint-block { color: #ccc; padding: .5rem .4rem 0; } ');

?>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('buyer_ilike')->hint(Yii::t('hipanel.finance.price', 'Who sold the tariff plan.')) ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('plan_owner_ilike') ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('plan_name_ilike') ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('object_name_ilike') ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('model_group_name_ilike') ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('model_partno_ilike') ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('price') ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('type')->dropDownList($search->model->typeOptions, ['prompt' => '--']) ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('unit')->dropDownList($search->model->unitOptions, ['prompt' => '--']) ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?php
    $currencies = $search->model->currencyOptions;
    $currencies = array_combine(array_keys($currencies), array_map(function ($k) {
        return StringHelper::getCurrencySymbol($k);
    }, array_keys($currencies)));
    ?>

    <?= $search->field('currency_in')->widget(\hiqdev\combo\StaticCombo::class, [
        'data' => $currencies,
        'hasId' => true,
        'multiple' => true,
    ]) ?>
</div>
