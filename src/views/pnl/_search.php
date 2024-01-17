<?php

/** @var AdvancedSearch $search */

use hipanel\modules\client\widgets\combo\ClientCombo;
use hipanel\widgets\AdvancedSearch;

?>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('charge_ids') ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('charge_id') ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('client') ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('type') ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('month') ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('has_no_type', ['options' => ['class' => 'form-group checkbox']])->checkbox(['class' => 'option-input']) ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('has_note', ['options' => ['class' => 'form-group checkbox']])->checkbox(['class' => 'option-input']) ?>
</div>
