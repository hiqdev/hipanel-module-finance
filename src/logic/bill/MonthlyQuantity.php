<?php

namespace hipanel\modules\finance\logic\bill;

use hipanel\modules\finance\forms\BillForm;
use hipanel\modules\finance\models\Bill;
use hipanel\modules\finance\models\Charge;
use hipanel\modules\server\models\Consumption;
use hiqdev\php\units\Quantity;
use Yii;

/**
 * Class MonthlyQuantity
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class MonthlyQuantity extends DefaultQuantityFormatter implements ContextAwareQuantityFormatter
{
    /** @var Bill|Charge */
    protected $model;

    /**
     * @inheritdoc
     */
    public function format(): string
    {
        $text = Yii::t('hipanel:finance', '{quantity, plural, one{# day} other{# days}}', ['quantity' => $this->getClientValue()]);

        return $text;
    }

    /**
     * @inheritdoc
     */
    public function getValue(): string
    {
        return $this->getQuantity() / $this->getNumberOfDays();
    }

    /**
     * @inheritdoc
     */
    public function getClientValue(): string
    {
        return round($this->getQuantity()->getQuantity() * $this->getNumberOfDays());
    }

    /**
     * @return false|string
     */
    protected function getNumberOfDays()
    {
        return date('t', strtotime($this->model->time));
    }

    /**
     * @param $context
     * @return ContextAwareQuantityFormatter
     */
    public function setContext($context): ContextAwareQuantityFormatter
    {
        if (!$context instanceof Bill && !$context instanceof Charge && !$context instanceof BillForm && !$context instanceof Consumption) {
            throw new \OutOfBoundsException(sprintf(
                'Context "%s" is not supported by Monthly quantity',
                get_class($context)
            ));
        }

        $this->model = $context;

        return $this;
    }
}
