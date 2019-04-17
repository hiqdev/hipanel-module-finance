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

/**
 * Class IPNumQuantity.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class IPNumQuantity extends DefaultQuantityFormatter
{
    public function format(): string
    {
        return Yii::t('hipanel:finance', '{quantity} IP', ['quantity' => $this->getValue()]);
    }
}
