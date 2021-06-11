<?php 

namespace hipanel\modules\finance\tests\acceptance\seller;

use hipanel\helpers\Url;
use hipanel\modules\finance\tests\_support\Page\bill\Create;
use hipanel\modules\finance\tests\_support\Page\bill\Update;
use hipanel\modules\finance\tests\_support\Page\bill\View;
use hipanel\tests\_support\Page\IndexPage;
use hipanel\tests\_support\Step\Acceptance\Seller;
use Codeception\Example;

class AddingChargesCest
{
    /**
     * @dataProvider provideBillData
     */
    public function ensureBillWillBeEditedWithNewChargesCorrectly(Seller $I, Example $example): void
    {
        $I->login();
        $createPage = new Create($I);
        $updatePage = new Update($I);
        $viewPage = new View($I);
        $exampleArray = iterator_to_array($example->getIterator());
        $I->needPage(Url::to('@bill/create'));
        
        $createPage->fillMainBillFields($exampleArray);
        $I->pressButton('Save');
        $billId = $createPage->seeActionSuccess();


        $updatePage->openBillUpdateById($billId);
        $createPage->addCharges( $exampleArray['charges']);
        $viewData = $createPage->getDataForViewCheck($exampleArray['charges']);
        $I->click('Save');
        
        $viewPage->viewBillById($billId);
        foreach ($viewData as $key => $charge) {
            $viewPage->ensureChargeViewContainsData($charge);
        }
    }

    private function provideBillData(): array
    {
        return [
            'client' => [
                'login'     => 'hipanel_test_user',
                'type'      => 'Monthly fee',
                'currency'  => '$',
                'sum'       =>  250,
                'quantity'  =>  1,
                'charges'   => [
                    'charge1'   => [
                        'class'     => 'Client',
                        'objectId'  => 'hipanel_test_user',
                        'type'      => 'Cash',
                        'sum'       => 250,
                        'quantity'  => 1,
                    ],
                    'charge2'   => [
                        'class'     => 'Client',
                        'objectId'  => 'hipanel_test_user1',
                        'type'      => 'Intercept fee',
                        'sum'       => 350,
                        'quantity'  => 1,
                    ],
                    'charge3'   => [
                        'class'     => 'Client',
                        'objectId'  => 'hipanel_test_user2',
                        'type'      => 'VAT',
                        'sum'       => 450,
                        'quantity'  => 1,
                    ],
                ],
            ],
        ];
    }
}
