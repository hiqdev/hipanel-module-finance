<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\tests\_support\Page\plan;

class Delete extends View
{
    public function deletePlan()
    {
        $I = $this->tester;

        $this->visitPlan();
        $I->click("//a[@href='/finance/plan/delete?id={$this->id}']");
        $I->acceptPopup();
        $I->closeNotification('Plan was successfully deleted');
    }
}
