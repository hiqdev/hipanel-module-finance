<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\widgets;

use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;
use Money\MoneyFormatter;
use Yii;
use yii\base\Widget;
use yii\bootstrap\ActiveField;

class PriceInput extends Widget
{
    /** @var Money */
    public $basePrice;

    /** @var Money */
    public $originalPrice;

    /** @var ActiveField */
    public $activeField;

    /** @var MoneyFormatter */
    private $moneyFormatter;

    public function __construct(DecimalMoneyFormatter $decimalMoneyFormatter, $config = [])
    {
        parent::__construct($config);
        $this->moneyFormatter = $decimalMoneyFormatter;
    }

    public function run()
    {
        $this->registerClientScript();
        $lang = Yii::$app->language;

        return $this->render('PriceInput', [
            'basePrice'     => $this->moneyFormatter->format($this->basePrice),
            'originalPrice' => $this->moneyFormatter->format($this->originalPrice),
            'activeField'   => $this->activeField,
            'currency'      => !$this->areCurrenciesSame() ? $this->originalPrice->getCurrency() : '',
            'lang'          => $lang . '-' . strtoupper($lang),
        ]);
    }

    private function areCurrenciesSame(): bool
    {
        return $this->basePrice->getCurrency()->equals($this->originalPrice->getCurrency());
    }

    private function registerClientScript()
    {
        $this->view->registerJs(<<<'JS'
            $('.price-input').on('change mouseup', function () {
                var basePrice = parseFloat($(this).val());
                if (isNaN(basePrice)) {
                    return false;
                }
                var originalObj = $(this).closest('td').find('.base-price'),
                    originalPrice = parseFloat(originalObj.attr('data-original-price')),
                    lang = originalObj.attr('data-lang'),
                    currency = originalObj.attr('data-currency'),
                    delta = basePrice - originalPrice;
                if (currency !== '') {
                    var originalPrice = new Intl.NumberFormat(lang, {style: 'currency', currency: currency}).format(originalPrice); 
                    originalObj.text(originalPrice).addClass('text-gray');
                    return;
                }
                originalObj.removeClass('text-success text-danger');
                originalObj.text(delta.toFixed(2)).addClass(delta >= 0 ? 'text-success' : 'text-danger');
            });
        
            $('.price-input').trigger('change');
JS
        );

        $this->view->registerCss('
            .base-price { font-weight: bold; }
            .form-group.form-group-sm { margin-bottom: 0; }
        ');
    }
}
