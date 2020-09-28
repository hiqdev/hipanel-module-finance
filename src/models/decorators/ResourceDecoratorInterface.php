<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\models\decorators;

interface ResourceDecoratorInterface
{
    public function getPrepaidQuantity();

    public function getOverusePrice();

    public function displayTitle();

    public function displayUnit();

    public function toUnit(): string;

    public function displayAmountWithUnit(): string;

    public function displayOverusePrice();

    public function displayPrepaidAmount();

    public function prepaidAmountType();
}
