<?php 

namespace hipanel\modules\finance\tests\acceptance\seller;

use hipanel\helpers\Url;
use Codeception\Example;
use hipanel\tests\_support\Step\Acceptance\Seller;
use hipanel\tests\_support\Page\Widget\Input\Input;
use hipanel\tests\_support\Page\Widget\Input\Select2;
use hipanel\modules\finance\tests\_support\Page\transfer\Create as transferCreate;
use hipanel\modules\finance\tests\_support\Page\bill\Create as billCreate;
use hipanel\modules\finance\tests\_support\Page\transfer\Index;

class InternalTransferCest
{
    /**
     * @var transferCreate
     */
    private $transferCreate;

    /**
     * @var billCreate
     */
    private $billCreate;

    /**
     * @var Index
     */
    private $index;

    public function _before(Seller $I)
    {
        $this->transferCreate = new transferCreate($I);
        $this->billCreate = new billCreate($I);
        $this->index = new Index($I);
    }

    /**
     * @dataProvider provideTransferData
     */
    public function rechargeAccount(Seller $I, Example $example): void
    {
        $I->needPage(Url::to('@bill/create'));
        $this->billCreate->fillMainBillFields($example['bill']);
        $I->pressButton('Save');
        $this->billCreate->seeActionSuccess();
        $I->wait(3);
    }

    /**
     * @dataProvider provideTransferData
     */
    public function ensureTransferIsWorkingCorrectly(Seller $I, Example $example): void
    {
        $I->login();
        $exampleArray = iterator_to_array($example->getIterator());

        $I->needPage(Url::to('@bill/create-transfer'));
        $I->see('Add internal transfer', 'h1');
        $this->ensureICantCreateTransferWithoutRequiredData($I);
        $this->ensureICanCreateInternalTransfer($I, $exampleArray);
        $I->click('Save');
        $this->index->seeTransferActionSuccess();
    }

    private function ensureICantCreateTransferWithoutRequiredData(Seller $I): void
    {
        $I->click('Save');
        $this->transferCreate->containsBlankFieldsError(['Sum' ,'Client', 'Receiver ID', 'Currency']);
    }

    private function ensureICanCreateInternalTransfer(Seller $I, $transferData): void
    {
        $this->transferCreate->fillMainInternalTransferFields($transferData['transfer']);
    }

    private function provideTransferData(): array
    {
        return [
            'transfers' => [
                'transfer' => [
                    'sum'        => 1000,
                    'client'     => 'hipanel_test_user',
                    'receiverId' => 'hipanel_test_user2',
                ],
                'bill' => [
                    'login'     => 'hipanel_test_user',
                    'type'      => 'Monthly fee',
                    'currency'  => '$',
                    'sum'       =>  1200,
                    'quantity'  =>  1,
                ]
            ],
        ];
    }
}
