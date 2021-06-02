<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2021, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\tests\_support\Page\bill;

use hipanel\helpers\Url;

class View extends Create
{
    public function viewBillById($billId): void
    {
        $this->tester->needPage(Url::to("@bill/view?id=$billId"));
    }
}
