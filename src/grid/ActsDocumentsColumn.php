<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\grid;

class ActsDocumentsColumn extends DocumentsColumn
{
    public $hideNewButton = true;

    protected function getRouteForUpdate()
    {
        return ['@purse/generate-acts'];
    }
}
