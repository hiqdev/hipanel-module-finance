<?php

namespace hipanel\modules\finance\tests\acceptance\manager;

use hipanel\helpers\Url;
use hipanel\tests\_support\Step\Acceptance\Manager;

class InstallmentPlanCreateCest
{
    public function _before(Manager $I): void
    {
        $I->login();
        $I->needPage(Url::to('@installment-plan/create'));
    }

    public function ensureCreatePageRendersFormFields(Manager $I): void
    {
        $I->see('Create Installment Plan');
        $I->seeElement('#installmentplan-part_id');
        $I->seeElement('#installmentplan-client_id');
        $I->seeElement('#installmentplan-currency');
        $I->seeElement('#installmentplan-monthly_sum');
        $I->seeElement('#installmentplan-since');
        $I->seeElement('#installmentplan-months');
    }
}
