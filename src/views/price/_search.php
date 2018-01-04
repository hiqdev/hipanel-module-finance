<?php

/** @var \hipanel\widgets\AdvancedSearch $search */

?>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('price') ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('plan_ilike') ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('type')->dropDownList($search->model->typeOptions, ['prompt' => '--']) ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('unit')->dropDownList($search->model->unitOptions, ['prompt' => '--']) ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('currency')->dropDownList($search->model->currencyOptions, ['prompt' => '--']) ?>
</div>
