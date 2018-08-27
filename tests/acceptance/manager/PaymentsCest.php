<?php

namespace hipanel\modules\finance\tests\acceptance\manager;


use hipanel\helpers\Url;
use hipanel\modules\finance\tests\_support\Page\bill\Create;
use hipanel\tests\_support\Step\Acceptance\Manager;

class PaymentsCest
{
    public function ensureBillPageWorks(Manager $I): void
    {
        $I->login();
        $I->needPage(Url::to('@bill'));
    }

    public function ensureICantCreateBillWithoutRequiredData(Manager $I): void
    {
        (new Create($I))->createBillWithoutData();
    }

    public function ensureICanCreateSimpledBill(Manager $I): void
    {
        (new Create($I))->createBill($this->getBillData());
    }

    public function ensureICantCreateDetailedBillWithoutDetailedData(Manager $I): void
    {
        (new Create($I))->createDetailedBillWithoutChargeData($this->getBillData());
    }

    public function ensureICanCreateDetailedBill(Manager $I): void
    {
        (new Create($I))->createDetailedBill($this->getBillData());
    }

    public function ensureICanUpdateBill(Manager $I)
    {
        (new Create($I))->updateBill();
    }

    public function ensureUpdatedBillWasSavedProperty(Manager $I)
    {
        (new Create($I))->checkUpdatedBill();
    }

    protected function getBillData(): array
    {
        return [
            'login'     => 'hipanel_test_admin@hiqdev.com',
            'type'      => 'monthly,monthly',
            'currency'  => '$',
            'sum'       =>  10,
            'quantity'  =>  1
        ];
    }
}
