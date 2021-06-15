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

use hipanel\tests\_support\Page\Authenticated;
use hipanel\tests\_support\Page\Widget\Grid;
use hipanel\helpers\Url;

class View extends Authenticated
{
    public function viewBillById(string $billId): void
    {
        $this->tester->needPage(Url::to("@bill/view?id=$billId"));
    }

    public function ensureChargeViewContainsData(array $chargeData): void
    {
        $I = $this->tester;

        $gridView = new Grid($I);
        $gridView->containsDataInTable($chargeData);
    }
}
