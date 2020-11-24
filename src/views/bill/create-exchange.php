<?php

use hipanel\modules\client\widgets\combo\ClientCombo;
use hipanel\modules\finance\forms\CurrencyExchangeForm;
use hipanel\modules\finance\models\ExchangeRate;
use hipanel\widgets\Box;
use hiqdev\combo\StaticCombo;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var View $this
 * @var bool $canSupport
 * @var CurrencyExchangeForm $model
 * @var ExchangeRate[] $rates
 */

$this->title = Yii::t('hipanel:finance', 'Create currency exchange');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Payments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<?php $form = ActiveForm::begin([
    'id' => 'rates-form',
    'enableClientValidation' => true,
    'options' => [
        'data-rates' => array_map(static fn(ExchangeRate $model) => $model->getAttributes(), $rates),
    ],
]) ?>
    <div class="bill-create-exchange">
        <div class="row">
            <div class="col-lg-6 col-md-8">
                <?php Box::begin() ?>
                <?= $canSupport ? $form->field($model, 'client_id')->widget(ClientCombo::class) : Html::activeHiddenInput($model, 'client_id') ?>
                <div class="row">
                    <div class="col-md-2">
                        <?= $form->field($model, 'sum')->textInput([
                            'data-attribute' => 'sum',
                            'value' => $model->sum ?: 1,
                        ])->label(false) ?>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($model, 'from')->widget(StaticCombo::class, [
                            'pluginOptions' => [
                                'select2Options' => [
                                    'allowClear' => false,
                                ],
                            ],
                            'inputOptions' => [
                                'data-attribute' => 'from',
                            ],
                        ])->label(false); ?>
                    </div>
                    <div class="col-md-1">
                        <i class="fa fa-long-arrow-right" style="padding: 10px"></i>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field($model, 'result')->textInput([
                            'data-attribute' => 'result',
                        ])->label(false) ?>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($model, 'to')->widget(StaticCombo::class, [
                            'pluginOptions' => [
                                'select2Options' => [
                                    'allowClear' => false,
                                ],
                            ],
                            'inputOptions' => [
                                'data-attribute' => 'to',
                            ],
                        ])->label(false);
                        ?>
                    </div>
                </div>
                <?php Box::end() ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?= Html::submitButton(Yii::t('hipanel', 'Create'), ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>
<?php ActiveForm::end() ?>

<?php $this->registerJs(<<<'JS'
(function ($, window, document, undefined) {
var pluginName = "currencyExchanger";

function Plugin(element, options) {
    var _this = this;
    this.element = $(element);

    this.currency = this.element.find('[data-attribute=from]');
    this.targetCurrency = this.element.find('[data-attribute=to]');
    this.sum = this.element.find('[data-attribute=sum]');
    this.targetSum = this.element.find('[data-attribute=result]');

    this.rates = {};

    this._name = pluginName;
    this.init();

    return {
        startQuerier: function () {
            return _this.startQuerier();
        }
    };
}

Plugin.prototype = {
    init: function () {
        this.rates = this.element.data('rates');

        this.attachListeners();
        this.updateCurrency();
    },
    attachListeners: function () {
        this.currency.on('change', this.updateTargetCurrency.bind(this));
        this.targetCurrency.on('change', this.updateTargetSum.bind(this));
        this.sum.on('keyup change', this.updateTargetSum.bind(this));
        this.targetSum.on('keyup change', this.updateSum.bind(this));
    },
    updateCurrency: function () {
        var currencies = $.map(this.rates, function (rate) {
            return rate.from;
        });

        this.setCurrencies(this.currency, $.unique(currencies));
        this.updateTargetCurrency();
    },
    getCorrespondingCurrencies: function (currency) {
        return $.map(this.rates, function (rate) {
            if (rate.from === currency) {
                return rate.to;
            }
        });
    },
    getCurrencyPair: function (from, to) {
        var pair = false;

        $.each(this.rates, function () {
            if (this.from === from && this.to === to) {
                pair = this;
                return false;
            }
        });

        return pair;
    },
    getRate: function () {
        var pair = this.getCurrencyPair(this.currency.val(), this.targetCurrency.val());

        if (pair === false) {
            return 1;
        }

        return pair.rate;
    },
    updateTargetSum: function () {
        var rate = this.getRate(),
            value = Math.round(this.sum.val() * rate * 100) / 100;

        if (isNaN(value)) {
            return;
        }

        this.targetSum.val(value);
    },
    updateSum: function () {
        var rate = this.getRate(),
            value = Math.round(this.targetSum.val() * (1/rate) * 100) / 100;

        if (isNaN(value)) {
            return;
        }

        this.sum.val(value);
    },
    updateTargetCurrency: function () {
        var availableCurrencies = this.getCorrespondingCurrencies(this.currency.val());

        this.setCurrencies(this.targetCurrency, availableCurrencies);
        this.updateTargetSum();
    },
    setCurrencies: function (element, currencies) {
        var data = $.map(currencies, function (currency) {
            return {id: currency, value: currency};
        });

        element.data('field').clearOptions();
        element.data('field').ensureOptions(data);
    }
};

$.fn[pluginName] = function (options) {
    if (!$(this).data("plugin_" + pluginName)) {
        $(this).data("plugin_" + pluginName, new Plugin(this, options));
    }

    return $(this).data("plugin_" + pluginName);
};
})(jQuery, window, document);

$('#rates-form').currencyExchanger();
JS
);
