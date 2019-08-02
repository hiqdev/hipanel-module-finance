<?php

use hipanel\modules\client\widgets\combo\ClientCombo;
use hipanel\modules\client\widgets\combo\SellerCombo;
use hipanel\modules\finance\helpers\CurrencyFilter;
use hipanel\modules\finance\widgets\combo\PlanCombo;
use hiqdev\combo\StaticCombo;
use hiqdev\yii2\daterangepicker\DateRangePicker;
use yii\helpers\Html;

/**
 * @var \yii\web\View
 * @var \hipanel\widgets\AdvancedSearch $search
 * @var array $billTypes
 * @var array $billGroupLabels
 */
?>

<?php if (Yii::$app->user->can('support')) : ?>
    <div class="col-md-4 col-sm-6 col-xs-12">
        <?= $search->field('client_id')->widget(ClientCombo::class) ?>
    </div>
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
    <div class="form-group">
        <?= Html::label(Yii::t('hipanel', 'Date')) ?>
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

<div class="col-md-4 col-sm-6 col-xs-12">
    <?php
    $types = [];
    foreach ($billTypes as $gtype => $category) {
        $item = [];
        foreach ($category as $key => $label) {
            $item[substr($key, strpos($key, ',') + 1)] = $label;
        }
        $types[$gtype] = $item;
    }

    echo $search->field('type_in')->widget(StaticCombo::class, [
        'data' => $types,
        'hasId' => true,
        'multiple' => true,
        'inputOptions' => [
            'groups' => $billGroupLabels,
        ],
    ]) ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('servers') ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('descr') ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('tariff_id')->widget(PlanCombo::class) ?>
</div>

<?php if (Yii::$app->user->can('support')) : ?>
    <div class="col-md-4 col-sm-6 col-xs-12">
        <?= $search->field('seller_id')->widget(SellerCombo::class) ?>
    </div>
<?php endif ?>
