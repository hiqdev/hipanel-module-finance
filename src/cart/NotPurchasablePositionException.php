<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\cart;

use yii\base\Exception;

/**
 * Interface PositionPurchasabilityValidatorInterface.
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
