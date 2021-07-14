<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\models\decorators\server;

use Yii;

class Traffic95ResourceDecorator extends AbstractServerResourceDecorator
{
    public function displayTitle()
    {
        return $this->displayTitleWithDirection(Yii::t('hipanel.finance.resource', '95 percentile traffic'));
    }

    public function displayValue()
    {
        return Yii::t('yii', '{nFormatted} Mbps', ['nFormatted' => $this->getPrepaidQuantity()]);
    }

    public function displayUnit()
    {
        return Yii::t('hipanel', 'Mbps');
    }

    public function toUnit(): string
    {
        return 'Mbps';
    }
}
