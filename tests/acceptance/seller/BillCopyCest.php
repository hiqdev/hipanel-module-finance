<?php

namespace hipanel\modules\finance\tests\acceptance\seller;

use hipanel\helpers\Url;
use Codeception\Example;
use hipanel\tests\_support\Page\IndexPage;
use hipanel\tests\_support\Step\Acceptance\Seller;
use hipanel\modules\finance\tests\_support\Page\bill\Create;
use hipanel\modules\finance\tests\_support\Page\bill\Index;
use hipanel\modules\finance\tests\_support\Page\bill\Copy;

class BillCopyCest
{
    private Create $createPage;
    private IndexPage $indexPage;
    private Copy $copyPage;

    public function _before(Seller $I): void
    {
        $this->createPage = new Create($I);
        $this->indexPage = new IndexPage($I);
        $this->copyPage = new Copy($I);
    }

    /**
     * @dataProvider provideDataBillWithCharge
     */
    public function createAndCopyBillWithCharges(Seller $I, Example $example): void
    {
        $exampleArray = iterator_to_array($example->getIterator());

        $billId = $this->createBill($I, $exampleArray);

        $this->copyPage->copyBill($billId);
        $exampleArray['id'] = $billId;
        $this->ensurePreviousBillDidntChange($I, $exampleArray);
    }

    private function ensurePreviousBillDidntChange(Seller $I, array $dataBill): void 
    {
        $I->needPage(Url::to('@bill/view?id=' . $dataBill['id']));
        $result = array_intersect_key($dataBill['charges']['charge2'], array_flip(['objectId', 'type']));
        $result[] = '$' . $dataBill['charges']['charge2']['sum'];
        $this->indexPage->gridView->ensureBillViewContainData($result);
    }

    private function createBill(Seller $I, $billData): ?string
    {
        $I->needPage(Url::to('@bill/create'));
        $this->createPage->fillMainBillFields($billData);
        if (isset($billData['charges'])) {
            $this->createPage->addCharges($billData['charges']);
        }

        $I->pressButton('Save');
        return $this->createPage->seeActionSuccess();
    }

    private function provideDataBillWithCharge(): array
    {
        return [
            'bill' => [
                'login'     => 'hipanel_test_user',
                'type'      => 'HDD',
                'currency'  => '$',
                'sum'       =>  -762.7,
                'quantity'  =>  1,
                'charges'   => [
                    'charge1'    => [
                        'class'    => 'Domain zone',
                        'objectId' => 'army',
                        'type'     => 'Common',
                        'sum'      => 712.80,
                        'quantity' => '1',
                    ],
                    'charge2'      =>[
                        'class'    => 'Domain',
                        'objectId' => 'bladeroot.net',
                        'type'     => 'PayPal',
                        'sum'      => 49.90,
                        'quantity' => '1',
                    ],
                ],
            ],
        ];
    }
}
