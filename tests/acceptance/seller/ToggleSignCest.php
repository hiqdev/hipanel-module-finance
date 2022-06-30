<?php

namespace hipanel\modules\finance\tests\acceptance\seller;

use Exception;
use hipanel\helpers\Url;
use Codeception\Example;
use hipanel\tests\_support\Step\Acceptance\Seller;
use hipanel\modules\finance\tests\_support\Page\bill\Create;

class ToggleSignCest
{
    private ?string $billId = null;

    /**
     * @dataProvider provideDataBill
     * @throws Exception
     */
    public function ensureToggleSignWorksAndICanCreateBill(Seller $I, Example $example): void
    {
        $I->login();
        $create = new Create($I);

        $I->needPage(Url::to('@bill/create'));
        $I->see('Create payment', 'h1');
        $I->see('Save', 'button');

        $billData = iterator_to_array($example->getIterator());

        $create->fillMainBillFields($billData);
        $create->addCharge($billData['charge']);
        $create->clickToggleSign();

        $I->click('Save');
        $this->billId = $create->seeActionSuccess();
    }

    /**
     * @dataProvider provideDataBill
     * @depends ensureToggleSignWorksAndICanCreateBill
     */
    public function ensureBillWasCreatedCorrectlyAndDeleteIt(Seller $I, Example $example): void
    {
        $I->login();

        $billData = iterator_to_array($example->getIterator());
        $chargeData = $billData['charge'];

        $create = new Create($I);

        $I->needPage(Url::to('@bill'). '?view=' . $this->billId);
        $I->see('$' . $chargeData['sum']);

        $create->deleteBillById($this->billId);
    }

    protected function provideDataBill(): array
    {
        return [
            'client' => [
                'login' => 'hipanel_test_user',
                'type' => 'PayPal',
                'sum' => '-777.00',
                'quantity' => '1',
                'charge' => [
                    'class' => 'Client',
                    'objectId' => 'hipanel_test_admin',
                    'type' => 'PayPal',
                    'sum' => '777.00',
                    'quantity' => '1',
                ],
            ],
        ];
    }
}
