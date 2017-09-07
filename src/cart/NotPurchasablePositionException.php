<?php

namespace hipanel\modules\finance\cart;

use yii\base\Exception;

/**
 * Interface PositionPurchasabilityValidatorInterface
 */
class NotPurchasablePositionException extends Exception
{
    public function getName()
    {
        return 'Position is not purchasable';
    }

    public function resolve()
    {
        return;
    }
}
