<?php

use hipanel\helpers\StringHelper;
use hipanel\modules\client\widgets\combo\ClientCombo;
use hipanel\modules\client\widgets\combo\SellerCombo;
use hipanel\modules\finance\widgets\TariffCombo;
use hipanel\modules\server\widgets\combo\ServerCombo;
use hipanel\widgets\DatePicker;
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
    $currencies = array_combine(array_keys($currencies), array_map(function ($k) {
        return StringHelper::getCurrencySymbol($k);
    }, array_keys($currencies)));

    echo $search->field('currency_in')->widget(\hiqdev\combo\StaticCombo::class, [
        'data' => $currencies,
        'hasId' => true,
        'multiple' => true,
    ]) ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <div class="form-group">
        <?= Html::label(Yii::t('hipanel', 'Date')) ?>
        <?= DatePicker::widget([
            'model' => $search->model,
            'attribute' => 'time_from',
            'attribute2' => 'time_till',
            'type' => DatePicker::TYPE_RANGE,
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd',
            ],
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

    echo $search->field('type_in')->widget(\hiqdev\combo\StaticCombo::class, [
        'data' => $types,
        'hasId' => true,
        'multiple' => true,
        'inputOptions' => [
            'groups' => $billGroupLabels,
        ],
    ]) ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('servers')->widget(ServerCombo::class, [
        'formElementSelector' => '.form-group',
        'multiple' => true,
    ]) ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('descr') ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('tariff_id')->widget(TariffCombo::class) ?>
</div>

<?php if (Yii::$app->user->can('support')) : ?>
    <div class="col-md-4 col-sm-6 col-xs-12">
        <?= $search->field('seller_id')->widget(SellerCombo::class) ?>
    </div>
<?php endif ?>
