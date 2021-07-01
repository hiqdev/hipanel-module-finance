<?php 

namespace hipanel\modules\finance\tests\acceptance\seller;

use hipanel\helpers\Url;
use Codeception\Example;
use hipanel\tests\_support\Step\Acceptance\Seller;
use hipanel\tests\_support\Page\Widget\Input\Select2;
use hipanel\modules\finance\tests\_support\Page\transfer\Create as TransferCreate;
use hipanel\modules\finance\tests\_support\Page\bill\Create as BillCreate;
use hipanel\modules\finance\tests\_support\Page\transfer\Index;

class InternalTransferCest
{
    private TransferCreate $transferCreate;
    private BillCreate $billCreate;
    private Index $index;

    public function _before(Seller $I): void
    {
        $this->transferCreate = new TransferCreate($I);
        $this->billCreate = new BillCreate($I);
        $this->index = new Index($I);
    }

    /**
     * IMPORTANT: this bill is needed because transfer can not be created when client has low balance
     * TODO: remove bill creation when transfer rules will be changed
     * @dataProvider provideTransferData
     */
    public function rechargeAccount(Seller $I, Example $example): void
    {
        $billData = iterator_to_array($example->getIterator()); 
        $billData = $this->updateBillIfUserHaveNegativeBalance($I, $billData);

        $I->needPage(Url::to('@bill/create'));
        $this->billCreate->fillMainBillFields($billData['bill']);
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

    private function updateBillIfUserHaveNegativeBalance(Seller $I, array $billInfo): array
    {
        $sum = $this->getTotalSumOnUserAccount($I, $billInfo);

        $sum = $this->transformSum($sum);
        if ($sum === null) {
            return $billInfo;
        }
        $billInfo['bill']['sum'] += ++$sum;

        return $billInfo;
    }

    private function getTotalSumOnUserAccount(Seller $I, array $billInfo): ?string
    {
        $I->needPage(Url::to('@finance/bill'));

        $this->index->filterBy(Select2::asTableFilter($I, 'Client'), $billInfo['transfer']['client']);
        $rowNumber = $this->index->gridView->getRowNumberByNameFromSummary('Total');

        return $I->grabTextFrom("//div[@class='summary']//tbody//tr[$rowNumber]//td//span");
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

    private function transformSum(string $currentBalance): ?string
    {
        $repl = [',' => ''];

        if (!strstr($currentBalance, '-')) {
            return null;
        }

        $currentBalance = substr_replace($currentBalance, '', 0, 2);
        return (int)strtr($currentBalance, $repl);
    }

    private function provideTransferData(): array
    {
        return [
            'payments' => [
                'transfer' => [
                    'sum'        => 1000,
                    'client'     => 'hipanel_test_user1',
                    'receiverId' => 'hipanel_test_user2',
                ],
                'bill' => [
                    'login'     => 'hipanel_test_user1',
                    'type'      => 'Monthly fee',
                    'currency'  => '$',
                    'sum'       =>  1000,
                    'quantity'  =>  1,
                ],
            ],
        ];
    }
}
