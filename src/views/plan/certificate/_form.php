<?php

/**
 * @var \yii\web\View $this
 * @var \hipanel\modules\finance\models\CertificatePrice[] $prices
 * @var \hipanel\modules\finance\models\CertificatePrice $price
 * @var \hipanel\modules\finance\models\CertificatePrice[][] $parentPrices
 * @var \hipanel\modules\finance\models\Plan $plan
 */

use hipanel\modules\finance\models\CertificatePrice;
use hipanel\widgets\Box;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use hipanel\helpers\Url;
use hipanel\modules\finance\widgets\PriceInput;
use Money\Formatter\DecimalMoneyFormatter;

$this->registerJs("
$('#tariff-create-form').on('afterValidate', function (event, messages) {
    if(typeof $('.has-error').first().offset() !== 'undefined') {
        $('html, body').animate({
            scrollTop: $('.has-error').first().offset().top
        }, 1000);
    }
});
");

$this->registerCss(<<<CSS
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
<?php if (!empty($prices)) : ?>
    <div class="tariff-create">
        <?php $form = ActiveForm::begin(array_filter([
            'id' => 'tariff-create-form',
            'action' => $action ?? null,
        ])) ?>

        <?php Box::begin() ?>
        <div class="row">
            <div class="col-md-12">
                <table id="main-table" class="table table-condensed price-table">
                    <thead>
                    <tr>
                        <?= Html::tag('th', Yii::t('hipanel:finance:tariff', 'Name')); ?>
                        <?php foreach (CertificatePrice::getTypes() as $name) : ?>
                            <?php foreach (CertificatePrice::getPeriods() as $period) : ?>
                                <?= Html::tag('th', Yii::t('hipanel:finance:tariff', '{operation} for {duration}', [
                                    'operation' => $name,
                                    'duration' => $period,
                                ])); ?>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $i = 0;
                    foreach ($prices as $object_id => $group) {
                        ?>
                        <tr>
                            <td><?= current($group)->object->label ?></td>
                            <?php foreach (CertificatePrice::getTypes() as $type => $name) : ?>
                                <?php $price = $group[$type] ?? null; ?>
                                <?php if ($price === null): ?>
                                    <?= str_repeat('<td></td>', count(CertificatePrice::getPeriods())) ?>
                                <?php continue; endif ?>
                                <?php $price->plan_id = $plan_id ?? $price->plan_id; ?>
                                <?php $price->isNewRecord = $price->isNewRecord ?? ($scenario === 'create' ? true : false) ?>
                                <?php if (!$price->isNewRecord && $scenario !== 'create' ): ?>
                                    <?= Html::activeHiddenInput($price, "[$i]id") ?>
                                <?php endif ?>
                                <?= Html::activeHiddenInput($price, "[$i]plan_id") ?>
                                <?= Html::activeHiddenInput($price, "[$i]object_id") ?>
                                <?= Html::activeHiddenInput($price, "[$i]currency") ?>
                                <?= $form->field($price, "[$i]price")
                                        ->hiddenInput(['value' => '1.00'])
                                        ->label(false)
                                ?>
                                <?= Html::activeHiddenInput($price, "[$i]type") ?>
                                <?= Html::activeHiddenInput($price, "[$i]unit") ?>
                                <?php $originalPrice = $parentPrices[$object_id][$type] ?? null; ?>
                                <?php foreach (CertificatePrice::getPeriods() as $period => $periodLabel) : ?>
                                    <td>
                                        <?= PriceInput::widget([
                                            'basePrice' => $price->getMoneyForPeriod($period),
                                            'originalPrice' => ($originalPrice ?? $price)->getMoneyForPeriod($period),
                                            'activeField' => $form->field($price, "[$i]sums[$period]")]) ?>
                                    </td>
                                <?php endforeach ?>
                                <?php ++$i ?>
                            <?php endforeach ?>
                        </tr>
                        <?php
                    } ?>
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
        'suggestionType' => Yii::t('hipanel.finance.suggestionTypes', 'certificate'),
    ]) ?>

    <br/>

    <?= Yii::t('hipanel.finance.price', 'You can return back to plan {backToPlan}', [
        'backToPlan' => Html::a($plan->name, Url::to(['@plan/view', 'id' => $plan->id])),
    ]) ?>
    <?= $box->endBody(); ?>
<?php endif ?>
