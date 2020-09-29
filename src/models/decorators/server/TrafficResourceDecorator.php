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

class TrafficResourceDecorator extends AbstractServerResourceDecorator
{
    public function displayTitle()
    {
        return $this->displayTitleWithDirection(Yii::t('hipanel:server:order', 'Traffic'));
    }

    public function displayValue()
    {
        return Yii::t('yii', '{nFormatted} GB', ['nFormatted' => $this->getPrepaidQuantity()]);
    }

    public function displayUnit()
    {
        return Yii::t('hipanel', 'GB');
    }

    public function toUnit(): string
    {
        return 'gb';
    }
}
