<?php

namespace hipanel\modules\finance\tests\_support\Page\price;

class Update extends View
{
    public function updatePrices(): void
    {
        $I = $this->tester;

        $this->loadPage();
        for ($i = 1; $i < 10; $i++) {
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
