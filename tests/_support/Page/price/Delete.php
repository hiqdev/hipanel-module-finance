<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\tests\_support\Page\price;

class Delete extends View
{
    public function deleteTemplatePrices(): void
    {
        $I = $this->tester;

        $this->loadPage();
        $I->click("//thead/tr/th/input[@name='selection_all']");
        $I->click('Delete');
        $I->acceptPopup();
        $I->closeNotification('Prices were successfully deleted');
        $I->see('No prices found');
    }
}
