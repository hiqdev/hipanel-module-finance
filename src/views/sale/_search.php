<?php

/** @var \hipanel\widgets\AdvancedSearch $search */
use hipanel\modules\client\widgets\combo\ClientCombo;
use hipanel\modules\client\widgets\combo\SellerCombo;
use hipanel\modules\finance\widgets\TariffCombo;

?>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('seller_id')->widget(SellerCombo::class) ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('buyer_id')->widget(ClientCombo::class)?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('object_type')->dropDownList($search->model->types, ['prompt' => '---']) ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('tariff_id')->widget(TariffCombo::class) ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('object_inilike') ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('object_label_ilike') ?>
</div>
