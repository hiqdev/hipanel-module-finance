<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\models;

use hiqdev\hiart\ActiveRecord;

/**
 * Value.
 * API returns this object as result of billing cost calculation.
 */
class Value extends ActiveRecord
{
    public function attributes()
    {
        return [
            'currency',
            'value',
            'price',
            'discounted_price',
        ];
    }
}
