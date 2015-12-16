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

/**
 * Class Module
 * @package hipanel\modules\finance
 */
class Module extends \hipanel\base\Module
{
    /**
     * Returns Cart component from Cart module
     * @return \hiqdev\yii2\cart\ShoppingCart
     */
    public function getCart()
    {
        /** @var \hiqdev\yii2\cart\Module $module */
        $module = Yii::$app->getModule('cart');
        return $module->getCart();
    }

    /**
     * Returns Merchant module.
     * @return \hiqdev\yii2\merchant\Module
     */
    public function getMerchant()
    {
        return Yii::$app->getModule('merchant');
    }
}
