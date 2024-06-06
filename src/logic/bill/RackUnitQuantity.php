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

use hipanel\modules\finance\models\BillableTimeInterface;
use Yii;

/**
 * Class RackUnitQuantity.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class RackUnitQuantity extends DefaultQuantityFormatter implements ContextAwareQuantityFormatter
{
    protected BillableTimeInterface $model;

    /**
     * {@inheritdoc}
     */
    public function format(): string
    {
        $units = $this->model->getQuantity() / ($this->model->getBillQuantity() ?: 1);
        $quantity = round($this->model->getBillQuantity() * $this->getNumberOfDays());

        return Yii::t('hipanel:finance',
            '{units, plural, one{# unit} other{# units}} &times; {quantity, plural, one{# day} other{# days}}',
            [
                'units' => $units,
                'quantity' => $quantity,
            ]);
    }

    public function getValue(): string
    {
        return $this->getQuantity()->getQuantity();
    }

    public function getClientValue(): string
    {
        return $this->getQuantity()->getQuantity();
    }

    private function getNumberOfDays(): string
    {
        return date('t', strtotime($this->model->getTime()));
    }

    public function setContext($context): ContextAwareQuantityFormatter
    {
        if (!$context instanceof BillableTimeInterface) {
            throw new \OutOfBoundsException(sprintf(
                'Context "%s" is not supported by Monthly quantity',
                get_class($context)
            ));
        }

        $this->model = $context;

        return $this;
    }
}
