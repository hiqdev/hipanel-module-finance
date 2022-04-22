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
 * Class SupportTimeQuantity.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class SupportTimeQuantity extends DefaultQuantityFormatter
{
    public function format(): string
    {
        $qty = $this->getQuantity()->getQuantity();

        return Yii::t('hipanel:finance', '{qty}', [
            'qty' => sprintf('%02d:%02d', (int)$qty, fmod($qty, 1) * 60),
        ]);
    }
}
