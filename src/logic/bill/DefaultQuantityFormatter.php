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

use hiqdev\php\units\Quantity;
use hiqdev\php\units\yii2\formatters\IntlFormatter;

/**
 * Class DefaultQuantityFormatter.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class DefaultQuantityFormatter implements QuantityFormatterInterface
{
    /**
     * @var Quantity
     */
    protected $quantity;

    /**
     * @var IntlFormatter
     */
    protected $intlFormatter;

    /**
     * MonthlyQuantity constructor.
     *
     * @param Quantity $quantity
     * @param IntlFormatter $intlFormatter
     */
    public function __construct(Quantity $quantity, IntlFormatter $intlFormatter)
    {
        $this->quantity = $quantity;
        $this->intlFormatter = $intlFormatter;
    }

    public function format(): string
    {
        return $this->intlFormatter->format($this->quantity);
    }

    public function getValue(): string
    {
        return $this->quantity->getQuantity();
    }

    public function getClientValue(): string
    {
        return $this->getValue();
    }

    protected function getQuantity(): Quantity
    {
        return $this->quantity;
    }
}
