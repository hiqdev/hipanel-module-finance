<?php

use hipanel\modules\client\widgets\combo\ClientCombo;
use hipanel\modules\client\widgets\combo\SellerCombo;
use hipanel\modules\finance\helpers\CurrencyFilter;
use hipanel\widgets\DatePicker;
use hiqdev\combo\StaticCombo;
use hipanel\models\Ref;

/**
 * @var \hipanel\widgets\AdvancedSearch
 */

$currencies = Ref::getList('type,currency');
$currencies = CurrencyFilter::addSymbolAndFilter($currencies);

?>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('name_ilike') ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('email_like') ?>
</div>

<?php if (Yii::$app->user->can('access-subclients')) : ?>
    <div class="col-md-4 col-sm-6 col-xs-12">
        <?= $search->field('client_id')->widget(ClientCombo::class) ?>
    </div>

    <div class="col-md-4 col-sm-6 col-xs-12">
        <?= $search->field('seller_id')->widget(SellerCombo::class) ?>
    </div>

    <?php if (in_array($uiModel->representation, ['balance', 'balances'], true)) : ?>
        <div class="col-md-4 col-sm-6 col-xs-12">
            <?= DatePicker::widget([
                'id' => 'balance_time-picker',
                'model' => $search->model,
                'attribute' => 'balance_time',
                'options' => [
                    'placeholder' => $search->model->getAttributeLabel('balance_time'),
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
