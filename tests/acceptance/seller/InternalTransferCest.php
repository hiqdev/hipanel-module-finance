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
        $createPage = new Create($I);
        $updatePage = new Update($I);
        $I->login();
        $this->ensureICanCreateBill($I, $createPage);
        $I->needPage(Url::to('@bill/create-transfer'));
        $I->see('Add internal transfer', 'h1');
        $this->ensureICantCreateTransferWithoutRequiredData($I, $createPage);
        $this->ensureICanCreateInternalTransfer($I, $createPage);
        $I->click('Save');
        $updatePage->seeTransferActionSuccess();
    }

    private function ensureICanCreateBill(Seller $I, Create $page): void
    {
        $I->needPage(Url::to('@bill/create'));
        $page->fillMainBillFields($this->getBillData());
        $I->pressButton('Save');
    }

    private function ensureICantCreateTransferWithoutRequiredData(Seller $I, Create $page): void
    {
        $I->click('Save');
        $page->containsBlankFieldsError(['Sum' ,'Client', 'Receiver ID', 'Currency']);
    }

   private function ensureICanCreateInternalTransfer(Seller $I, Create $page): void
    {
        $transferData = $this->getTransferData();
        $page->fillMainInternalTransferFields($transferData['client']);
    }

    private function getTransferData(): array
    {
        return [
            'client' => [
                'sum'          => 1000,
                'client'       => 'hipanel_test_user',
                'receiver ID'  => 'hipanel_test_user2',
            ],
        ];
    }

    private function getBillData(): array
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
