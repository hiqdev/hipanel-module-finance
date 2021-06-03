<?php

namespace hipanel\modules\finance\tests\acceptance\seller;

use hipanel\helpers\Url;
use Codeception\Example;
use hipanel\tests\_support\Page\IndexPage;
use hipanel\tests\_support\Step\Acceptance\Seller;
use hipanel\modules\finance\tests\_support\Page\bill\Create;

class BillCopyCest
{
    /**
     * @dataProvider provideDataBill
     */
     public function ensureICanCreateAndCopyBill(Seller $I, Example $example)
    {
        $I->login();
        $billId = $this->ensureICanCreateBill($I, iterator_to_array($example->getIterator()));
        $this->ensureICanCopyBill($I, $billId);
    } 
    
    /**
     * @dataProvider provideDataBillWithCharge
     */
    public function ensureCopiedBillWillBeCorrect(Seller $I, Example $example)
    {
        $example = iterator_to_array($example->getIterator());

        $billId = $this->ensureICanCreateBill($I, $example);
        $copyId = $this->ensureICanCopyBill($I, $billId);
        $billSum = $this->ensureICanEditCopiedBill($I, $copyId, $example['charges']);
        $checkId = $this->ensureICanSaveUpdatedBill($I);

        $this->ensureBillWasCreatedCorrectly($I, $checkId, $example, $billSum);
        $this->ensureChargesWasCreatedCorrectly($I, $checkId, $example['charges']);
        $this->ensurePreviousBillDidntChange($I, $billId, $example['charges']);
    }

    private function ensureICanCreateBill(Seller $I, $billData): int
    {
        $createPage = new Create($I);
        $I->needPage(Url::to('@bill/create'));
        $createPage->fillMainBillFields($billData);
        if (isset($billData['charges'])) {
            $createPage->addCharge($billData['charges']['charge1']);
        }
        $I->pressButton('Save');
        return $createPage->seeActionSuccess();
    }

    private function ensureICanCopyBill(Seller $I, $id)
    {
        $pageCopy = new Create($I);
        $I->needPage(Url::to('@bill/copy?id=' . $id));
        $I->pressButton('Save');
        return $pageCopy->seeActionSuccess();
    }

    private function ensureICanEditCopiedBill(Seller $I, $id, $billData)
    {
        $pageCopy = new Create($I);
        $I->needPage(Url::to('@bill/update?id=' . $id));
        $pageCopy->addCharge($billData['charge2']);
        $sum = $pageCopy->getAndSetBillTotalSum();
        return -$sum;
    }

    private function ensureICanSaveUpdatedBill(Seller $I)
    {
        $pageCopy = new Create($I);
        $I->pressButton('Save');
        return $pageCopy->seeUpdateSuccess();
    }

    private function ensureBillWasCreatedCorrectly(Seller $I, $id, $dataBill, $sum)
    {
        $index = new IndexPage($I);
        $I->needPage(Url::to('@bill/view?id=' . $id));
        $tempResult = array_intersect_key($dataBill, array_flip(['login', 'type']));
        $tempResult[] = -$sum;
        $index->gridView->ensureBillViewContainsData($tempResult);
    }

    private function ensureChargesWasCreatedCorrectly(Seller $I, $id, $dataBill)
    {
        $indexPage = new IndexPage($I);
        $I->needPage(Url::to('@bill/view?id=' . $id));
        $result = array_intersect_key($dataBill['charge1'], array_flip(['type']));
        $result[] = '$' . $dataBill['charge2']['sum'];
        $indexPage->gridView->ensureChargeViewContainsData($result);

        $result = array_intersect_key($dataBill['charge2'], array_flip(['objectId', 'type']));
        $result[] = '$' . $dataBill['charge2']['sum'];
        $indexPage->gridView->ensureChargeViewContainsData($result);
    }

    private function ensurePreviousBillDidntChange(Seller $I, $id, $dataBill): void 
    {
        $indexPage = new IndexPage($I);
        $I->needPage(Url::to('@bill/view?id=' . $id));
        $result = array_intersect_key($dataBill['charge2'], array_flip(['objectId', 'type']));
        $result[] = '$' . $dataBill['charge2']['sum'];
        $indexPage->gridView->ensureBillViewDontContainData($result);
    }

    private function provideDataBill(): array
    {
        return [
            'bill' => [
                'login'     => 'hipanel_test_user',
                'type'      => 'HDD',
                'currency'  => '$',
                'sum'       =>  -44,
                'quantity'  =>  1,
            ],
        ];
    }

    private function provideDataBillWithCharge(): array
    {
        return [
            'bill' => [
                'login'     => 'hipanel_test_user',
                'type'      => 'HDD',
                'currency'  => '$',
                'sum'       =>  -44,
                'quantity'  =>  1,
                'charges'   => [
                    'charge1'    => [
                        'class'    => 'Domain zone',
                        'objectId' => 'army',
                        'type'     => 'Common',
                        'sum'      => 44,
                        'quantity' => '1',
                    ],
                    'charge2'      =>[
                        'class'    => 'Domain',
                        'objectId' => 'bladeroot.net',
                        'type'     => 'PayPal',
                        'sum'      => 56,
                        'quantity' => '1',
                    ],
                ]
            ],
        ];
    }
}
