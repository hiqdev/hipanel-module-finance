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
    public string $billId;

    private Create $createPage;
    private IndexPage $indexPage;
    private Copy $copyPage;

    public function _before(Seller $I)
    {
        $this->createPage = new Create($I);
        $this->indexPage = new IndexPage($I);
        $this->copyPage = new Copy($I);
    }

    /**
     * @dataProvider provideDataBill
     */
     public function ensureICanCreateAndCopyBillWithoutCharges(Seller $I, Example $example): void
    {
        $I->login();
        $this->createPage->createBill(iterator_to_array($example->getIterator()));
    } 
    
    /**
     * @dataProvider provideDataBillWithCharge
     */
    public function createAndCopyBillWithCharges(Seller $I, Example $example): void
    {
        $exampleArray = iterator_to_array($example->getIterator());

        $billId = $this->createPage->createBill($exampleArray);

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
                ],
            ],
        ];
    }
}
