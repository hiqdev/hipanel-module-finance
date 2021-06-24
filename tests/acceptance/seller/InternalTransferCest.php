<?php 

namespace hipanel\modules\finance\tests\acceptance\seller;

use hipanel\helpers\Url;
use Codeception\Example;
use hipanel\tests\_support\Step\Acceptance\Seller;
use hipanel\tests\_support\Page\Widget\Input\Input;
use hipanel\tests\_support\Page\Widget\Input\Select2;
use hipanel\modules\finance\tests\_support\Page\transfer\Create as TransferCreate;
use hipanel\modules\finance\tests\_support\Page\bill\Create as BillCreate;
use hipanel\modules\finance\tests\_support\Page\transfer\Index;
use hipanel\tests\_support\Page\IndexPage;

class InternalTransferCest
{
    private TransferCreate $transferCreate;
    private BillCreate $billCreate;
    private Index $index;
    private IndexPage $indexPage;

    public function _before(Seller $I): void
    {
        $this->transferCreate = new TransferCreate($I);
        $this->billCreate = new BillCreate($I);
        $this->index = new Index($I);
        $this->indexPage = new IndexPage($I);
    }

    /**
     * IMPORTANT: this bill is needed because transfer can not be created when client has low balance
     * TODO: remove bill creation when transfer rules will be changed
     * @dataProvider provideTransferData
     */
    public function rechargeAccount(Seller $I, Example $example): void
    {
        $billData = iterator_to_array($example->getIterator()); 
        $billData = $this->checkIfUserHaveNegativeBalance($I, $billData);

        $I->needPage(Url::to('@bill/create'));
        $this->billCreate->fillMainBillFields($example['bill']);
        $this->billCreate->addCharges($billData['bill']['charges']);
        $this->billCreate->getAndSetBillSumFromCharges();
        $this->billCreate->clickToggleSign();
        $I->pressButton('Save');
        $this->billCreate->seeActionSuccess();
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

    private function checkIfUserHaveNegativeBalance(Seller $I, array $billInfo)
    {
        $I->needPage(Url::to('@finance/bill'));

        $this->indexPage->filterBy(Select2::asTableFilter($I, 'Client'), $billInfo['transfer']['client']);
        $rowNumber = $this->indexPage->gridView->getRowNumberByNameFromSummary('Total');
        $sum = $I->grabTextFrom("//div[@class='summary']//tbody//tr[$rowNumber]//td//span");

        $sum = $this->transofrmSum($sum);
        if ($sum) {
            $sum = (int)$sum;
            $billInfo['bill']['charges']['charge2'] = $billInfo['bill']['charges']['charge1'];
            
            $billInfo['bill']['charges']['charge2']['type'] = 'PayPal';
            $billInfo['bill']['charges']['charge2']['sum'] = ++$sum;
        }

        return $billInfo;
    }

    private function ensureICantCreateTransferWithoutRequiredData(Seller $I): void
    {
        $I->click('Save');
        $this->transferCreate->containsBlankFieldsError();
    }

    private function ensureICanCreateInternalTransfer(Seller $I, array $transferData): void
    {
        $this->transferCreate->fillMainInternalTransferFields($transferData['transfer']);
    }

    private function transofrmSum(string $currentBalance): ?string
    {

        $repl = [',' => ''];
        if(similar_text($currentBalance, '-')) {
            $currentBalance = substr_replace($currentBalance, '', 0, 2);
            $currentBalance = strtr($currentBalance, $repl);
        } else {
            return null;
        }

        return $currentBalance;
    }

    private function provideTransferData(): array
    {
        return [
            'transfers' => [
                'transfer' => [
                    'sum'        => 1000,
                    'client'     => 'hipanel_test_user1',
                    'receiverId' => 'hipanel_test_user2',
                ],
                'bill' => [
                    'login'     => 'hipanel_test_user1',
                    'type'      => 'Monthly fee',
                    'currency'  => '$',
                    'sum'       =>  0,
                    'quantity'  =>  1,
                    'charges'   => [
                        'charge1'   => [
                            'class' => 'Client',
                            'objectId' => 'hipanel_test_user1',
                            'type' => 'Monthly fee',
                            'sum' => 1000,
                            'quantity' => '1',
                        ],
                    ],
                ],
            ],
        ];
    }
}
