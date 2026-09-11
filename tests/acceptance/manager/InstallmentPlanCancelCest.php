<?php

namespace hipanel\modules\finance\tests\acceptance\manager;

use hipanel\helpers\Url;
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

    /**
     * part_id/client_id are Combo (select2) widgets with no options until an ajax
     * search runs, so fillField() can't set them. Instead we inject an <option> with
     * the desired value directly into the underlying <select> and trigger change —
     * the same technique apply-to-all.js itself already uses on Combo fields.
     */
    private function createInstallmentPlan(Manager $I): int
    {
        $partId = 348490210;
        $clientId = 360113632;

        $I->amOnPage(Url::to(['@installment-plan/create']));
        $I->executeJS("$('#installmentplan-part_id').append('<option value=\"{$partId}\" selected></option>').trigger('change');");
        $I->executeJS("$('#installmentplan-client_id').append('<option value=\"{$clientId}\" selected></option>').trigger('change');");
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
}
