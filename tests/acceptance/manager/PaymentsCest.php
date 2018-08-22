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
        (new Create($I))->createDetailedBillWithoutDetailedData($this->getBillData());
    }

    public function ensureICanCreateDetailedBill(Manager $I): void
    {
        (new Create($I))->createDetailedBill($this->getBillData());
    }

    protected function getBillData(): array
    {
        return [
            'login'     => 'hipanel_test_user@hiqdev.com',
            'type'      => 'monthly,monthly',
            'currency'  => '$',
            'sum'       =>  10,
            'quantity'  =>  1
        ];
    }
}
