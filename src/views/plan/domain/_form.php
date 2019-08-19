<?php

/**
 * @var \yii\web\View $this
 * @var \hipanel\modules\finance\models\DomainZonePrice[][] $zonePrices
 * @var \hipanel\modules\finance\models\DomainServicePrice[] $servicePrices
 * @var \hipanel\modules\finance\models\DomainZonePrice[][] $parentZonePrices
 * @var \hipanel\modules\finance\models\DomainServicePrice[] $parentServicePrices
 * @var \hipanel\modules\finance\models\Plan $plan
 */

use hipanel\modules\finance\models\DomainZonePrice;
use hipanel\modules\finance\models\DomainServicePrice;
use hipanel\widgets\Box;
use hipanel\modules\finance\widgets\PriceInput;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use hipanel\helpers\Url;

$this->registerJs("
$('#tariff-create-form').on('afterValidate', function (event, messages) {
    if(typeof $('.has-error').first().offset() !== 'undefined') {
        $('html, body').animate({
            scrollTop: $('.has-error').first().offset().top
        }, 1000);
    }
});
");

$this->registerCss(<<<'CSS'
.text-gray {
    color: gray !important;
}
.price-table input::-webkit-outer-spin-button,
.price-table input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
.price-table input[type=number] {
    -moz-appearance: textfield;
}
#main-table input {
    padding: 5px 3px !important;
}
#main-table div.col-md-6 {
    padding-left: 4px !important;
}
CSS
);
?>
<?php if (!empty($zonePrices)) : ?>
    <div class="tariff-create">
        <?php $form = ActiveForm::begin(array_filter([
            'id' => 'tariff-create-form',
            'action' => $action ?? null,
            'validateOnChange' => true,
        ])) ?>

        <?php Box::begin() ?>
        <div class="row">
            <div class="col-md-12">
                <table id="main-table" class="table table-condensed price-table">
                    <thead>
                    <tr>
                        <th>
                            <h4 class="box-title" style="display: inline-block;">&nbsp;
                                <?= Yii::t('hipanel:finance', 'Prices') ?>
                            </h4>
                        </th>
                    </tr>
                    <tr>
                        <?= Html::tag('th', ''); ?>
                        <?php foreach (DomainZonePrice::getTypes() as $name) : ?>
                            <?= Html::tag('th', $name); ?>
                        <?php endforeach; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $i = 0; ?>
                    <?php foreach ($zonePrices as $zone => $group) : ?>
                        <tr>
                            <td><?= current($group)->object->name ?></td>
                            <?php foreach (DomainZonePrice::getTypes() as $type => $name) : ?>
                                <?php $originalPrice = $parentZonePrices[$zone][$type] ?? null; ?>
                                <?php $price = $group[$type] ?? $originalPrice ?? null; ?>

                                <?php if ($price === null): ?>
	                                <td></td>
                                    <?php continue; ?>
                                <?php endif; ?>

                                <?php $price->plan_id = $plan_id ?? $price->plan_id; ?>
                                <?= Html::activeHiddenInput($price, "[$i]id") ?>
                                <?= Html::activeHiddenInput($price, "[$i]plan_id") ?>
                                <?= Html::activeHiddenInput($price, "[$i]object_id") ?>
                                <?= Html::activeHiddenInput($price, "[$i]currency") ?>
                                <?= Html::activeHiddenInput($price, "[$i]type") ?>
                                <?= Html::activeHiddenInput($price, "[$i]unit") ?>
                                <td>
                                    <?= PriceInput::widget([
                                        'basePrice' => $price->getMoney(),
                                        'originalPrice' => ($originalPrice ?? $price)->getMoney(),
                                        'activeField' => $form->field($price, "[$i]price")]) ?>
                                </td>
                                <?php ++$i; ?>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <table class="table table-condensed price-table">
                    <thead>
                    <tr>
                        <th>
                            <h4 class="box-title" style="display: inline-block;">&nbsp;
                                <?= DomainServicePrice::getLabel() ?>
                            </h4>
                        </th>
                    </tr>
                    <tr>
                        <?php foreach (DomainServicePrice::getOperations() as $name) : ?>
                            <?= Html::tag('th', $name); ?>
                        <?php endforeach; ?>
                    </tr>
                    </thead>
                    <tbody>
                        <?php $i = 0; ?>
                        <tr>
                        <?php foreach ($servicePrices as $type => $price) : ?>
                            <?php $originalPrice = $parentServicePrices[$type] ?? null; ?>
                            <?php $price->plan_id = $plan_id ?? $price->plan_id; ?>
                            <?= Html::activeHiddenInput($price, "[$i]id") ?>
                            <?= Html::activeHiddenInput($price, "[$i]plan_id") ?>
                            <?= Html::activeHiddenInput($price, "[$i]currency") ?>
                            <?= Html::activeHiddenInput($price, "[$i]type") ?>
                            <?= Html::activeHiddenInput($price, "[$i]unit") ?>
                            <td>
                                <?= PriceInput::widget([
                                    'basePrice' => $price->getMoney(),
                                    'originalPrice' => ($originalPrice ?? $price)->getMoney(),
                                    'activeField' => $form->field($price, "[$i]price")]) ?>
                            </td>
                            <?php ++$i; ?>
                        <?php endforeach; ?>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <?php Box::end() ?>

        <?php Box::begin(['options' => ['class' => 'box-solid']]) ?>
        <div class="row">
            <div class="col-md-12 no">
                <?= Html::submitButton(Yii::t('hipanel', 'Save'), ['class' => 'btn btn-success']) ?>
                <?= Html::button(Yii::t('hipanel', 'Cancel'), ['class' => 'btn btn-default', 'onclick' => 'history.go(-1)']) ?>
            </div>
        </div>
        <?php Box::end() ?>
        <?php ActiveForm::end(); ?>
    </div>
<?php else: ?>
    <?php $box = Box::begin([
        'title' => Yii::t('hipanel.finance.price', 'No price suggestions for this object'),
    ]) ?>
    <?= Yii::t('hipanel.finance.price', 'We could not suggest any new prices of type "{suggestionType}" for the selected object. Probably, they were already created earlier or this suggestion type is not compatible with this object type', [
        'suggestionType' => Yii::t('hipanel.finance.suggestionTypes', 'domain'),
    ]) ?>

    <br/>

    <?= Yii::t('hipanel.finance.price', 'You can return back to plan {backToPlan}', [
        'backToPlan' => Html::a($plan->name, Url::to(['@plan/view', 'id' => $plan->id])),
    ]) ?>
    <?= $box->endBody(); ?>
<?php endif ?>
