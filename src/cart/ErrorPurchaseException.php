<?php

/*
 * Finance Plugin for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2016, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\cart;

use yii\base\Exception;

/**
 * Exception represents an exception occurred during cart position purchase.
 */
class ErrorPurchaseException extends Exception
{
    /**
     * @var AbstractCartPosition
     */
    public $position;

    /**
     * ErrorPurchaseException constructor.
     *
     * @param string $message
     * @param AbstractCartPosition $position
     * @param Exception $previous
     */
    public function __construct($message, $position, Exception $previous)
    {
        $this->position = $position;
        parent::__construct($message, null, $previous);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Error occurred during item "' . $this->position->getName() . '"" purchase';
    }
}
