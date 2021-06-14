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
    public function viewBillById(string $billId): void
    {
        $this->tester->needPage(Url::to("@bill/view?id=$billId"));
    }

    public function ensureBillViewContainsData(array $elements): void
    {
        foreach ($elements as $tableContent) {
            $this->tester->see($tableContent, "//div[@class='box']//table");
        }
    }

    public function ensureChargeViewContainsData(array $chargeData): void
    {
        foreach ($chargeData as $key => $billData) {
                $this->tester->see($billData, '//div[@class="table-responsive"]//tr');
        }
    }
}
