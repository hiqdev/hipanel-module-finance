<?php

/** @var \hipanel\widgets\AdvancedSearch $search */

use hipanel\modules\client\widgets\combo\ClientCombo;
use hiqdev\combo\StaticCombo;

?>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('name_ilike') ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('client_id')->widget(ClientCombo::class) ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('type')->widget(\hipanel\widgets\RefCombo::class, [
        'gtype' => "type,tariff",
        'i18nDictionary' => 'hipanel:finance',
        'multiple' => true,
    ]) ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('note_ilike')->textInput(['placeholder' => Yii::t('hipanel', 'Note')]) ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('states')->widget(StaticCombo::class, [
        'data' => $search->model->stateOptions,
        'hasId' => false,
        'multiple' => true,
    ]) ?>
</div>
