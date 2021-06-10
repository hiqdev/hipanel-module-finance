<?php

namespace hipanel\modules\finance\tests\acceptance\seller;

use hipanel\helpers\Url;
use Codeception\Example;
use hipanel\tests\_support\Page\IndexPage;
use hipanel\tests\_support\Step\Acceptance\Seller;
use hipanel\modules\finance\tests\_support\Page\bill\Create;
use hipanel\modules\finance\tests\_support\Page\bill\Index;

class BillCopyCest
{
    /**
     * @var Create
     */
    private $create;

    public function _before(Seller $I)
    {
        $this->create = new Create($I);
    }
    /**
     * @dataProvider provideDataBill
     */
     public function ensureICanCreateAndCopyBillWithoutCharges(Seller $I, Example $example)
    {
        $I->login();
        $this->create->createAndCopyBill(iterator_to_array($example->getIterator()));
    } 
    
    /**
     * @dataProvider provideDataBillWithCharge
     */
    public function ensureCopiedBillWithChargesWillBeCorrect(Seller $I, Example $example)
    {
        $exampleArray = iterator_to_array($example->getIterator());

        $copyId = $this->create->createAndCopyBill($exampleArray);
        $billId = --$copyId;

        $this->ensurePreviousBillDidntChange($I, $billId, $exampleArray['charges']);
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
