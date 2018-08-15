<?php

namespace hipanel\modules\finance\logic\bill;

use hipanel\base\Model;
use hipanel\modules\finance\models\Bill;
use hipanel\modules\finance\models\Charge;
use hiqdev\php\units\Quantity;
use hiqdev\php\units\yii2\formatters\IntlFormatter;

/**
 * Class DefaultQuantityFormatter
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
