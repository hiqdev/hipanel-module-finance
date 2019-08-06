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

/**
 * Class CartIsBrokenException
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class CartIsBrokenException extends NotPurchasableException
{
    public function getName()
    {
        return 'Something is badly broken in the cart';
    }
}
