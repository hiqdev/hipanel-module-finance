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

class Update extends View
{
    public function updatePrices(): void
    {
        $I = $this->tester;

        $this->loadPage();
        for ($i = 1; $i < 10; ++$i) {
            $I->click("(//tbody/tr/td/input[@type='checkbox'])[{$i}]");
        }
        $I->click('Update');
        $I->waitForText('Update');
        $this->fillRandomPrices('templateprice');
        $I->click('Save');
        $I->closeNotification('Prices were successfully updated');
        $this->seeRandomPrices();
    }
}
