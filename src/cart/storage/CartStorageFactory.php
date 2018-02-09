<?php

namespace hipanel\modules\finance\cart\storage;

use Yii;
use yii\web\User;
use yii\di\Instance;

/**
 * Class CartStorageFactory
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
