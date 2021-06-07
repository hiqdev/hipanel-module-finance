<?php 

namespace hipanel\modules\finance\tests\acceptance\seller;

use hipanel\helpers\Url;
use Codeception\Example;
use hipanel\tests\_support\Step\Acceptance\Seller;
use hipanel\tests\_support\Page\Widget\Input\Input;
use hipanel\tests\_support\Page\Widget\Input\Select2;
use hipanel\modules\finance\tests\_support\Page\bill\Create;
use hipanel\modules\finance\tests\_support\Page\bill\Update;

class InternalTransferCest
{
     /**
     * @var Create
     */
    private $create;

    /**
     * @var Update
     */
    private $update;

    public function _before(Seller $I)
    {
        $this->create = new Create($I);
        $this->update = new Update($I);
    }

    /**
     * @dataProvider provideTransferData
     */
    public function ensureIndexPageWorks(Seller $I, Example $example): void
    {
        #$createPage = new Create($I);
        #$updatePage = new Update($I);
        $I->login();
        $exampleArray = iterator_to_array($example->getIterator());
        $this->ensureICanCreateBill($I, $exampleArray['bill']);
        $I->needPage(Url::to('@bill/create-transfer'));
        $I->see('Add internal transfer', 'h1');
        $this->ensureICantCreateTransferWithoutRequiredData($I);
        $this->ensureICanCreateInternalTransfer($I, $exampleArray);
        $I->click('Save');
        $this->update->seeTransferActionSuccess();
    }

    private function ensureICanCreateBill(Seller $I, $billData): void
    {
        $I->needPage(Url::to('@bill/create'));
        $this->create->fillMainBillFields($billData);
        $I->pressButton('Save');
    }

    private function ensureICantCreateTransferWithoutRequiredData(Seller $I): void
    {
        $I->click('Save');
        $this->create->containsBlankFieldsError(['Sum' ,'Client', 'Receiver ID', 'Currency']);
    }

   private function ensureICanCreateInternalTransfer(Seller $I, $transferData): void
    {
        $this->create->fillMainInternalTransferFields($transferData['transfer']);
    }

    private function provideTransferData(): array
    {
        return [
            'payments' => [
                'transfer' => [
                    'sum'          => 1000,
                    'client'       => 'hipanel_test_user',
                    'receiverId'  => 'hipanel_test_user2',
                ],
                'bill' => [
                    'login'     => 'hipanel_test_user',
                    'type'      => 'PayPal',
                    'currency'  => '$',
                    'sum'       =>  1000,
                    'quantity'  =>  1,
                ],
            ],
        ];
    }
}
