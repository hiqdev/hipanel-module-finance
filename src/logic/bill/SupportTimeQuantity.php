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
        return Yii::t('hipanel:finance', '{quantity} hour(s)', [
            'quantity' => Yii::$app->formatter->asTime(ceil($this->getQuantity()->getQuantity() * 3600), 'short')
        ]);
    }
}
