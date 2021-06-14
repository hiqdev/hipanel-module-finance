<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\tests\_support\Page\bill;

use hipanel\helpers\Url;

class Update extends Create
{
    /**
     *  Checks whether a bill was updated successfully.
     */
    public function seeActionSuccess(): ?string
    {
        $I = $this->tester;

        $I->closeNotification('Bill was updated successfully');
        $I->seeInCurrentUrl('/finance/bill?id');

        return null;
    }

    public function openBillUpdateById(string $billId): void
    {
        $this->tester->needPage(Url::to("@bill/update?id=$billId"));
    }
}
