<?php
namespace hipanel\modules\finance\tests\_support\Page\price\certificate;

class Update extends Create
{
    public function updatePrices(int $id): void
    {
        $I = $this->tester;

        $this->loadPage($id);
        $I->click('Update');
        $I->waitForText('Update');
        $this->fillRandomPrices();
        $I->click('Save');
        $I->closeNotification('Prices were successfully updated');
        $this->seeRandomPrices();
    }
}
