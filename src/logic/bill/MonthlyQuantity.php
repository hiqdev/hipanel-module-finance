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

use hipanel\modules\finance\models\HasTimeAttributeInterface;
use Yii;

/**
 * Class MonthlyQuantity.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class MonthlyQuantity extends DefaultQuantityFormatter implements ContextAwareQuantityFormatter
{
    protected HasTimeAttributeInterface $model;

    /**
     * {@inheritdoc}
     */
    public function format(): string
    {
        $unitName = $this->getQuantity()->getUnit()->getName();
        if ($unitName === 'hour') {
            return Yii::t('hipanel:finance', '{quantity, plural, one{# hour} few{# hours} other{# hours}}', ['quantity' => $this->getQuantity()->getQuantity()]);
        }

        return Yii::t('hipanel:finance', '{quantity, plural, one{# day} other{# days}}', ['quantity' => $this->getClientValue()]);
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(): string
    {
        return $this->getQuantity()->getQuantity() / $this->getNumberOfDays();
    }

    /**
     * {@inheritdoc}
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
        return date('t', strtotime($this->model->getTime()));
    }

    public function setContext($context): ContextAwareQuantityFormatter
    {
        if (!$context instanceof HasTimeAttributeInterface) {
            throw new \OutOfBoundsException(sprintf(
                'Context "%s" is not supported by Monthly quantity',
                get_class($context)
            ));
        }

        $this->model = $context;

        return $this;
    }
}
