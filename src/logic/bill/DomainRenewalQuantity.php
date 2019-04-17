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

class DomainRenewalQuantity extends DefaultQuantityFormatter
{
    /**
     * {@inheritdoc}
     */
    public function format(): string
    {
        return Yii::t('hipanel:finance', '{quantity, plural, one{# year} other{# years}}', [
            'quantity' => $this->getClientValue(),
        ]);
    }
}
