<?php

/*
 * Finance Plugin for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2014-2015, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance;

use Yii;

class Module extends \hipanel\base\Module
{
    /**
     * Returns Cart component from Cart module.
     */
    public function getCart()
    {
        return Yii::$app->getModule('cart')->getCart();
    }

    /**
     * Returns Merchant module.
     */
    public function getMerchant()
    {
        return Yii::$app->getModule('merchant');
    }
}
