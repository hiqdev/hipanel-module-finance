<?php

/**
 * @var \hipanel\widgets\AdvancedSearch $search
 * @var IndexPageUiOptions $uiModel
 */
use hipanel\modules\client\widgets\combo\ClientCombo;
use hipanel\widgets\RefCombo;
use hiqdev\combo\StaticCombo;
use hipanel\modules\finance\helpers\CurrencyFilter;

?>

<?php
$currencies = $this->context->getCurrencyTypes();
$currencies = CurrencyFilter::addSymbolAndFilter($currencies);
?>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('name_ilike') ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('client_id')->widget(ClientCombo::class) ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('buyer_in')->widget(ClientCombo::class) ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('type_in')->widget(RefCombo::class, [
        'gtype' => 'type,tariff',
        'i18nDictionary' => 'hipanel:finance',
        'multiple' => true,
    ]) ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('object_inilike') ?>
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

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('currency_in')->widget(StaticCombo::class, [
        'data' => $currencies,
        'hasId' => true,
        'multiple' => true,
    ]) ?>

</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('fee_ge')->input('number', [
        'step' => 0.01,
        'placeholder' => $search->model->getAttributeLabel('fee_ge'),
    ]) ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('fee_le')->input('number', [
        'step' => 0.01,
        'placeholder' => $search->model->getAttributeLabel('fee_le'),
    ]) ?>
</div>

<?php if ($uiModel->representation === 'manager'): ?>
    <div class="col-md-4 col-sm-6 col-xs-12 checkbox">
        <?= $search->field('is_sold')->widget(StaticCombo::class, [
            'hasId' => true,
            'multiple' => false,
            'data' => [
                '' => Yii::t('hipanel', 'All'),
                '0' => Yii::t('hipanel', 'No'),
                '1' => Yii::t('hipanel', 'Yes'),
            ],
        ]) ?>
    </div>
<?php endif ?>

