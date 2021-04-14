<?php

use hipanel\modules\client\widgets\combo\ClientCombo;
use hipanel\modules\client\widgets\combo\SellerCombo;
use hipanel\modules\finance\helpers\CurrencyFilter;
use hipanel\widgets\DateTimePicker;
use hiqdev\combo\StaticCombo;
use hipanel\models\Ref;

/**
 * @var \hipanel\widgets\AdvancedSearch
 */
?>

<?php
$currencies = Ref::getList('type,currency');
$currencies = CurrencyFilter::addSymbolAndFilter($currencies);
?>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('name_like') ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('email_like') ?>
</div>

<?php if (Yii::$app->user->can('support')) : ?>
    <div class="col-md-4 col-sm-6 col-xs-12">
        <?= $search->field('client_id')->widget(ClientCombo::class) ?>
    </div>

    <div class="col-md-4 col-sm-6 col-xs-12">
        <?= $search->field('seller_id')->widget(SellerCombo::class) ?>
    </div>

    <?php if (in_array($uiModel->representation, ['balance', 'balances'], true)) : ?>
        <div class="col-md-4 col-sm-6 col-xs-12">
            <?= DateTimePicker::widget([
                'id' => 'balance_time-picker',
                'model' => $search->model,
                'attribute' => 'balance_time',
                'clientOptions' => [
                    'autoclose' => true,
                    'minView' => 2,
                    'format' => 'yyyy-mm-dd',
                ],
            ]) ?>
        </div>
    <?php endif ?>

    <?php if ($uiModel->representation === 'balance') : ?>
        <div class="col-md-4 col-sm-6 col-xs-12">
            <?= $search->field('currency')->widget(StaticCombo::class, [
                'data' => $currencies,
                'hasId' => true,
                'multiple' => false,
            ]) ?>
        </div>
    <?php endif ?>
<?php endif ?>
