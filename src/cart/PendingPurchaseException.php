<?php

/*
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2016, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\cart;

use yii\base\Exception;

/**
 * Exception represents an pending state of position occurred during cart purchase.
 */
class PendingPurchaseException extends Exception
{
    /**
     * @var AbstractCartPosition
     */
    public $position;

    /**
     * PendingPurchaseException constructor.
     *
     * @param string $message
     * @param AbstractCartPosition $position
     * @param Exception $previous
     */
    public function __construct($message, $position, Exception $previous = null)
    {
        $this->position = $position;
        parent::__construct($message, 0, $previous);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Item "' . $this->position->getName() . '"" is pending for additional actions';
    }
}
