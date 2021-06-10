<?php 

namespace hipanel\modules\finance\tests\acceptance\seller;

use hipanel\helpers\Url;
use Codeception\Example;
use hipanel\tests\_support\Step\Acceptance\Seller;
use hipanel\tests\_support\Page\Widget\Input\Input;
use hipanel\tests\_support\Page\Widget\Input\Select2;
use hipanel\modules\finance\tests\_support\Page\transfer\Create;
use hipanel\modules\finance\tests\_support\Page\transfer\Index;

class InternalTransferCest
{
     /**
     * @var Create
     */
    private $create;

    /**
     * @var Index
     */
    private $index;

    public function _before(Seller $I)
    {
        $this->create = new Create($I);
        $this->index = new Index($I);
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
        $this->create->containsBlankFieldsError(['Sum' ,'Client', 'Receiver ID', 'Currency']);
    }

    private function ensureICanCreateInternalTransfer(Seller $I, $transferData): void
    {
        $this->create->fillMainInternalTransferFields($transferData['transfer']);
    }

    private function provideTransferData(): array
    {
        return [
            'transfers' => [
                'transfer' => [
                    'sum'        => 1,
                    'client'     => 'hipanel_test_user',
                    'receiverId' => 'hipanel_test_user2',
                ],
            ],
        ];
    }
}
