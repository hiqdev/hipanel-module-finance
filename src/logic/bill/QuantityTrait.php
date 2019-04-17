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

use Yii;

trait QuantityTrait
{
    public function getQuantity()
    {
        if (!$this->isNewRecord && isset($this->type)) {
            /** @var QuantityFormatterFactoryInterface $factory */
            $factory = Yii::$container->get(QuantityFormatterFactoryInterface::class);
            $billQty = $factory->create($this);

            if ($billQty !== null) {
                return $billQty->getClientValue();
            }
        }
    }
}
