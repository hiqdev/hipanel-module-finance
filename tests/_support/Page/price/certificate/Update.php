<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\tests\_support\Page\price\certificate;

class Update extends Create
{
    public function updatePrices(): void
    {
        $I = $this->tester;

        $this->loadPage();
        $I->click('Update');
        $I->waitForText('Update');
        $this->fillRandomPrices('');
        $I->click('Save');
        $I->closeNotification('Prices were successfully updated');
        $this->seeRandomPrices();
    }
}
