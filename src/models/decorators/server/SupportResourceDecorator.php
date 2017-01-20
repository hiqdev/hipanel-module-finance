<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\models\decorators\server;

use Yii;

class SupportResourceDecorator extends AbstractServerResourceDecorator
{
    public function displayTitle()
    {
        return Yii::t('hipanel:finance:tariff', '24/7 support');
    }

    public function displayPrepaidAmount()
    {
        $quantity = $this->getPrepaidQuantity();

        // todo: uncomment after adding commerce pages
//        if ($quantity == 1) {
//            return Yii::t('hipanel:finance:tariff', 'Bronze');
//        } elseif ($quantity == 1.5) {
//            return Yii::t('hipanel:finance:tariff', 'Silver');
//        } elseif ($quantity == 2) {
//            return Yii::t('hipanel:finance:tariff', 'Gold');
//        } elseif ($quantity == 3) {
//            return Yii::t('hipanel:finance:tariff', 'Platinum');
//        }

        return Yii::t('hipanel:finance:tariff', '{n, plural, one{# hour} other{# hours}}', ['n' => $quantity]);
    }
}
