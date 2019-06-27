<?php

namespace hipanel\modules\finance\cart;

use yii\base\Exception;

class ErrorMultiCurrencyException extends ErrorPurchaseException
{
    /**
     * {@inheritDoc}
     */
    public function __construct($message, $purchase, Exception $previous = null)
    {
        parent::__construct(!empty($message) ? $message : 'Can not add this item to cart because of different currency', $purchase, $previous);
    }
}
