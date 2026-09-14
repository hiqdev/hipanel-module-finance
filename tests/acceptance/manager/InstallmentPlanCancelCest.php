<?php

namespace hipanel\modules\finance\tests\acceptance\manager;

use hipanel\helpers\Url;
use hipanel\modules\stock\tests\_support\Page\model\Create as ModelCreate;
use hipanel\modules\stock\tests\_support\Page\part\Create as PartCreate;
use hipanel\tests\_support\Page\Widget\Input\Select2;
use hipanel\tests\_support\Step\Acceptance\Manager;

class InstallmentPlanCancelCest
{
    public function _before(Manager $I): void
    {
        $I->login();
    }

    public function ensureICanCancelAnInstallmentPlanFromItsViewPage(Manager $I): void
    {
        $id = $this->createInstallmentPlan($I);

        $I->amOnPage(Url::to(['@installment-plan/view', 'id' => $id]));
        $I->see('Cancel');

        $I->click('Cancel');
        $I->acceptPopup();
        $I->wait(1);

        $I->see('Installment plan has been cancelled');
        $I->see('Cancelled');
    }

    private function createInstallmentPlan(Manager $I): int
    {
        $serial = $this->createFreshPart($I);
        [, $clientLogin] = $I->getClientCredentials();

        $I->amOnPage(Url::to(['@installment-plan/create']));
        // part_id/client_id are ajax-search Combo (select2) widgets — fillField() can't
        // set them directly. Search by the fresh part's own unique serial and by the
        // test client's stable login (never a raw DB id — see Select2::setValue()).
        (new Select2($I, '#installmentplan-part_id'))->setValue($serial);
        (new Select2($I, '#installmentplan-client_id'))->setValue($clientLogin);
        $I->selectOption('#installmentplan-currency', 'usd');
        $I->fillField('#installmentplan-monthly_sum', '5.00');
        $I->fillField('#installmentplan-since', '2026-09-01');
        $I->fillField('#installmentplan-months', '1');
        $I->click('Create');
        $I->wait(1);

        $url = $I->grabFromCurrentUrl();
        preg_match('/id=(\d+)/', (string) $url, $matches);
        $id = (int) ($matches[1] ?? 0);
        if ($id === 0) {
            $I->fail('Could not determine created installment plan id from redirect URL: ' . $url);
        }

        return $id;
    }

    /**
     * A brand-new part is guaranteed to be installment-eligible — it can't already be
     * rented under a tariff or carry any prior installment plan. Never reuse a fixed
     * fixture part_id here: it's live, mutable data and can change state between runs
     * (this test broke once already because the previously hardcoded part turned out
     * to be rented under a real tariff on the shared dev DB).
     */
    /**
     * Returns the serial number of a freshly created part, used to find it in the
     * installment plan's part_id Combo search.
     */
    private function createFreshPart(Manager $I): string
    {
        $uid = uniqid();
        $serial = 'HQD410_TEST_PART' . $uid;

        $modelPage = new ModelCreate($I);
        $I->amOnPage(Url::to('@model/create'));
        $modelPage->fillModelFields([
            'type'     => 'SSD',
            'brand'    => 'Kingston',
            'group_id' => '1-2TB OLD SSD',
            'model'    => 'HQD410_TEST_MODEL' . $uid,
            'partno'   => $partno = 'HQD410_TEST_PARTNO' . $uid,
            'url'      => 'test_url',
            'short'    => 'HQD-410 installment cancel test',
            'descr'    => 'HQD-410 installment cancel test',
        ]);
        $I->pressButton('Save');
        $modelPage->seeModelWasCreated();

        $partPage = new PartCreate($I);
        $I->amOnPage(Url::to('@part/create'));
        $partPage->fillPartFields([
            'partno'     => $partno,
            'src_id'     => 'TEST-DS-01',
            'dst_id'     => 'TEST-DS-02',
            'serials'    => $serial,
            'move_descr' => 'HQD-410 installment cancel test',
            'price'      => '0',
            'currency'   => 'usd',
            'company_id' => 'Other',
        ]);
        $partPage->pressSaveButton();
        $partPage->seePartWasCreated();

        return $serial;
    }
}
