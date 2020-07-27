<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\logic\bill;

use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;
use Money\Currency;
use Yii;

/**
 * Class MoneyQuantity.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class MoneyQuantity extends DefaultQuantityFormatter
{
    public function format(): string
    {
        $formatter = Yii::$container->get(DecimalMoneyFormatter::class);
        $sum = $formatter->format($this->getAmount());

        return Yii::$app->formatter->asCurrency($sum, $this->getCurrency());
    }

    public function getAmount(): Money
    {
        $quantity = $this->getQuantity();
        $amount = ceil(100*$quantity->getQuantity());

        return new Money($amount, new Currency($this->getCurrency()));
    }

    public function getCurrency(): string
    {
        return strtoupper($this->getQuantity()->getUnit()->getName());
    }
}
