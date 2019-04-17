<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\cart\storage;

use Yii;
use yii\web\User;

/**
 * Class CartStorageFactory.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class CartStorageFactory
{
    /**
     * CartStorageFactory constructor.
     *
     * @param User $user
     * @return \yii\web\Session
     */
    public static function forUser(User $user)
    {
        if ($user->getIsGuest()) {
            return Yii::$app->session;
        }

        return Yii::createObject(['class' => RemoteCartStorage::class, 'sessionCartId' => 'yz\shoppingcart\ShoppingCart']);
    }
}
