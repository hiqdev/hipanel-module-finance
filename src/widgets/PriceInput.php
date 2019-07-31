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

use hipanel\modules\finance\models\DomainZonePrice;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Formatter\IntlLocalizedDecimalFormatter;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;
use Money\MoneyFormatter;
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

        return $this->render('PriceInput', [
            'basePrice' => $this->moneyFormatter->format($this->basePrice),
            'originalPrice' => $this->moneyFormatter->format($this->originalPrice),
            'activeField' => $this->activeField,
            'currency' => !$this->areCurrenciesSame() ? $this->originalPrice->getCurrency() : '',
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
                var price = parseFloat($(this).val());
                if (isNaN(price)) return false;
                var base = $(this).closest('td').find('.base-price'),
                    basePrice = parseFloat(base.attr('data-original-price')),
                    currency = base.attr('data-currency'),
                    delta = price - basePrice;
                if (currency !== '') {
                    base.text(delta.toFixed(2) + currency).addClass('text-gray');
                    return;
                }
                base.removeClass('text-success text-danger');
                base.text(delta.toFixed(2)).addClass(delta >= 0 ? 'text-success' : 'text-danger');
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
