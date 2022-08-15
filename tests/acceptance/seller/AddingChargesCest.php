<?php

namespace hipanel\modules\finance\tests\acceptance\seller;

use hipanel\helpers\Url;
use hipanel\modules\finance\tests\_support\Page\bill\Create;
use hipanel\modules\finance\tests\_support\Page\bill\Update;
use hipanel\modules\finance\tests\_support\Page\bill\View;
use hipanel\tests\_support\Step\Acceptance\Seller;
use Codeception\Example;

class AddingChargesCest
{
    /**
     * @dataProvider provideBillData
     */
    public function ensureBillWillBeEditedWithNewChargesCorrectly(Seller $I, Example $example): void
    {
        $I->markTestSkipped("Moved to PW");
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
        $createPage->addCharges($exampleArray['charges']);
        $viewData = $this->getDataForViewCheck($exampleArray['charges']);
        $I->click('Save');

        $viewPage->viewBillById($billId);
        foreach ($viewData as $charge) {
            $viewPage->ensureChargeViewContainsData($charge);
        }
    }

    private function getDataForViewCheck(array $chargeData): array
    {
        foreach ($chargeData as $key => $billData) {
            $viewData[] = array_intersect_key($chargeData, array_flip(['objectId', 'type', 'sum']));
        }

        return $viewData;
    }

    private function provideBillData(): array
    {
        return [
            'client' => [
                'login' => 'hipanel_test_user',
                'type' => 'Monthly fee',
                'currency' => '$',
                'sum' => 250,
                'quantity' => 1,
                'charges' => [
                    'charge1' => [
                        'class' => 'Client',
                        'objectId' => 'hipanel_test_user',
                        'type' => 'Cash',
                        'sum' => 250,
                        'quantity' => 1,
                    ],
                    'charge2' => [
                        'class' => 'Client',
                        'objectId' => 'hipanel_test_user1',
                        'type' => 'Certificate purchase',
                        'sum' => 350,
                        'quantity' => 1,
                    ],
                    'charge3' => [
                        'class' => 'Client',
                        'objectId' => 'hipanel_test_user2',
                        'type' => 'Negative balance correction',
                        'sum' => 450,
                        'quantity' => 1,
                    ],
                ],
            ],
        ];
    }
}
