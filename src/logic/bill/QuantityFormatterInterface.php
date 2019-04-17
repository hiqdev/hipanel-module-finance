<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\logic\bill;

interface QuantityFormatterInterface
{
    /**
     * Returns textual user friendly representation of the quantity.
     * E.g. 20 days, 30 GB, 1 year.
     *
     * @return string
     */
    public function format(): string;

    /**
     * Returns numeric to be saved in DB.
     *
     * @return string
     */
    public function getValue(): string;

    /**
     * Returns numeric user friendly representation of the quantity.
     *
     * @return string
     */
    public function getClientValue(): string;
}
