<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\widgets\combo;

use hipanel\modules\client\widgets\combo\ContactCombo;

/**
 * Class RequisiteCombo.
 */
class RequisiteCombo extends ContactCombo
{
    /** {@inheritdoc} */
    public $type = 'finance/requisite';

    /** {@inheritdoc} */
    public $url = '/finance/requisite/search';
}
