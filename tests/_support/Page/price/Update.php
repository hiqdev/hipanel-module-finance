<?php

namespace hipanel\modules\finance\tests\_support\Page\price;

class Update extends View
{
    private function loadUpdate()
    {
        $I = $this->tester;

        $I->click('Update');
        $I->waitForText('Update');
    }

        private function saveAndSeeUpdate()
    {
        $I = $this->tester;

        $I->click('Save');
        $I->closeNotification('Prices were successfully updated');
        $this->seeRandomPrices();
    }

    public function updatePrice($id)
    {
        $I = $this->tester;

        $this->loadPage($id);
        for ($i = 1; $i < 10; $i++) {
            $I->click("(//tbody/tr/td/input[@type='checkbox'])[{$i}]");
        }
        $this->loadUpdate();
        $this->fillRandomPrices('templateprice');
        $this->saveAndSeeUpdate();
    }

    public function updateCertificatePrice($id)
    {
        $this->loadPage($id);
        $this->loadUpdate();
        $this->fillRandomCertificatePrices();
        $this->saveAndSeeUpdate();
    }
}
