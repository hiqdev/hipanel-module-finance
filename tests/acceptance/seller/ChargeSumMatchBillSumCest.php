<?php 

namespace hipanel\modules\finance\tests\acceptance\seller;

use hipanel\modules\finance\tests\_support\Page\bill\Create;
use hipanel\tests\_support\Step\Acceptance\Seller;
use Codeception\Example;
use hipanel\helpers\Url;

class BillChargeSumCest 
{
    /**
     * @dataProvider getBillData
     */
    public function ensureChargeSumMatchBillSum(Seller $I, Example $example)
    {
        $I->login();
        $page = new Create($I);
        $I->needPage(Url::to('@bill/create'));
        $exampleArray = iterator_to_array($example->getIterator());
        $page->createNewBill($exampleArray);
        $I->cantSee('Bill sum must match charges sum: -' . $exampleArray['sum']);
    }

    private function getBillData(): array
    {
        return [
            'client' => [
                'login'     => 'hipanel_test_user',
                'type'      => 'Monthly fee',
                'currency'  => '$',
                'sum'       =>  762.7,
                'quantity'  =>  1,
                'charges'   => [ 
                    'charge1'    => [
                        'class'     => 'Client',
                        'objectId'  => 'hipanel_test_user1',
                        'type'      => 'Monthly fee',
                        'sum'       => -712.80,
                        'quantity'  => 1,
                    ],
                    'charge2'      =>[
                        'class'     => 'Client',
                        'objectId'  => 'hipanel_test_user2',
                        'type'      => 'VAT',
                        'sum'       => -49.90,
                        'quantity'  => 1,
                    ],
                ],
            ],
        ];
    }
}
