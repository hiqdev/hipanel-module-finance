<?php 

namespace hipanel\modules\finance\tests\acceptance\seller;

use hipanel\helpers\Url;
use hipanel\tests\_support\Step\Acceptance\Seller;
use hipanel\tests\_support\Page\Widget\Input\Input;
use hipanel\tests\_support\Page\Widget\Input\Select2;
use hipanel\modules\finance\tests\_support\Page\bill\Create;
use hipanel\modules\finance\tests\_support\Page\bill\Update;

class InternalTransferCest
{
    public function ensureIndexPageWorks(Seller $I): void
    {
        $indexPage = new Create($I);
        $indexUpdate = new Update($I);
        $I->login();
        $this->ensureICanCreateBill($I, $indexPage);
        $I->needPage(Url::to('@bill/create-transfer'));
        $I->see('Add internal transfer', 'h1');
        $this->ensureICantCreateTransferWithoutRequiredData($I, $indexPage);
        $this->ensureICanCreateInternalTransfer($I, $indexPage);
        $I->click('Save');
        $indexUpdate->seeTransferActionSuccess();
    }

    private function ensureICanCreateBill(Seller $I, $Page): void
    {
        $I->needPage(Url::to('@bill/create'));
        $Page->fillMainBillFields($this->getBillData());
        $I->pressButton('Save');
    }

    private function ensureICantCreateTransferWithoutRequiredData(Seller $I, $Page): void 
    {
        $I->click('Save');
        $Page->containsBlankFieldsError(['Sum' ,'Client', 'Receiver ID', 'Currency']);
    }

   private function ensureICanCreateInternalTransfer(Seller $I, $Page): void
    {
        $transferData = $this->getTransferData();
        $Page->fillMainInternalTransferFields($transferData['client']);
    }

    private function getTransferData(): array
    {
        return [
            'client' => [
                'Sum'          => 1000,
                'Client'       => 'hipanel_test_user',
                'Receiver ID'  => 'hipanel_test_user2',
            ],
        ];
    }
    protected function getBillData(): array
    {
        return [
            'login'     => 'hipanel_test_user',
            'type'      => 'PayPal',
            'currency'  => '$',
            'sum'       =>  1000,
            'quantity'  =>  1,
        ];
    }
}
