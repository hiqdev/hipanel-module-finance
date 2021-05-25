<?php 

namespace hipanel\modules\finance\tests\acceptance\seller;

use hipanel\helpers\Url;
use hipanel\tests\_support\Step\Acceptance\Seller;
use hipanel\tests\_support\Page\Widget\Input\Input;
use hipanel\tests\_support\Page\Widget\Input\Select2;
use hipanel\modules\finance\tests\_support\Page\bill\Create;
use hipanel\modules\finance\tests\_support\Page\bill\Update;
use hipanel\modules\finance\tests\acceptance\seller\PaymentsCest;

class InternalTransferCest
{
    public function ensureIndexPageWorks(Seller $I): void
    {
        $indexPage = new Create($I);
        $indexUpdate = new Update($I);
        $paymnet = new PaymentsCest($I);
        $I->login();
        $paymnet->ensureICanCreateSimpleBill($I);
        $I->needPage(Url::to('@bill/create-transfer'));
        $I->see('Add internal transfer', 'h1');
        $this->ensureICantCreateTransferWithoutRequiredData($I, $indexPage);
        $this->ensureICanCreateInternalTransfer($I, $indexPage);
        $I->click('Save');
        $indexUpdate->seeTransferActionSuccess();
    }

    private function ensureICantCreateTransferWithoutRequiredData(Seller $I, $Page): void 
    {
        $I->click('Save');
        $Page->containsBlankFieldsError(['Sum' ,'Client', 'Receiver ID', 'Currency']);
    }

   private function ensureICanCreateInternalTransfer(Seller $I, $Page): void
    {
        $transferData = $this->getTransferData();
        $Page->fillMainInternalTransferFields($transferData);
    }

    private function getTransferData(): array
    {
        return [
            'Sum'          => 1000,
            'Client'       => 'hipanel_test_user',
            'Receiver ID'  => 'hipanel_test_user2',
        ];
    }
}
