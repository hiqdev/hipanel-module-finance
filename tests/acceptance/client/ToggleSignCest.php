<?php

namespace hipanel\modules\finance\tests\acceptance\client\PaymentCreation;

use hipanel\helpers\Url;
use hipanel\tests\_support\Step\Acceptance\Seller;
use hipanel\modules\finance\tests\_support\Page\bill\Create;
use Codeception\Example;

class PaymentCreationCest
{
    /**
     * @dataProvider provideDataBill
     */
    public function ensureIndexPageWorks(Seller $I, Example $example)
    {
        $createPage = new Create($I);
        $createPage->visit();
        $I->see('Create payment', 'h1');
        $I->see('Save', 'button');
        $createPage->fillMainBillFields(iterator_to_array($example->getIterator()));
        $charge = $this->provideDataCharge();
        $createPage->addCharge($charge);
        $createPage->clickToggleSign();
        $I->click('Save');
        $this->ensureBillWasCreatedAndDeleteIt($I, $charge);
    }
    private function ensureBillWasCreatedAndDeleteIt(Seller $I, $chargeData): void
    {
        $newId = new Create($I);
        $id = $newId->seeActionSuccess();
        $I->see('$' . $chargeData['sum']);
        $newId->deleteBillById($id);
    }
    private function provideDataCharge(): array
    {
        return [  
            'class' => 'Client',
            'objectId' => 'hipanel_test_admin',
            'type' => 'PayPal',
            'sum' => '777.00',
            'quantity' => '1'
        ];
        
    }
    public function provideDataBill(): array
    {
        return [
            'client' => [ 'login' => 'hipanel_test_user',
                'type' => 'PayPal',
                'sum' => '-777.00',
                'quantity' => '1']
        ];
    }
}
