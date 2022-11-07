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

use hipanel\modules\finance\models\Charge;
use Yii;

/**
 * Class MonthlyQuantity.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class RackUnitQuantity extends MonthlyQuantity
{
    /** @var Charge */
    protected $model;

    /**
     * {@inheritdoc}
     */
    public function format(): string
    {
        $text = Yii::t('hipanel:finance',
            '{units, plural, one{# unit} other{# units}} &times; {quantity, plural, one{# day} other{# days}}',
            [
                'units' => $this->model->quantity / ($this->model->bill_quantity ?: 1),
                'quantity' => round($this->model->bill_quantity * $this->getNumberOfDays()),
            ]);

        return $text;
    }

    public function getValue(): string
    {
        return $this->getQuantity()->getQuantity();
    }

    public function getClientValue(): string
    {
        return $this->getQuantity()->getQuantity();
    }
}
