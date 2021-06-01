<?php 

namespace hipanel\modules\finance\tests\acceptance\seller;

use hipanel\helpers\Url;
use hipanel\modules\finance\tests\_support\Page\bill\Create;
use hipanel\modules\finance\tests\_support\Page\bill\Update;
use hipanel\tests\_support\Page\IndexPage;
use hipanel\tests\_support\Step\Acceptance\Seller;
use Codeception\Example;

class AddingChargesCest
{
    /**
     * @dataProvider getBillData
     */
    public function ensureBillWillBeEditedWithNewChargesCorrectly(Seller $I, Example $example): void
    {
        $I->login();
        $page = new Create($I);
        $indexPage = new IndexPage($I);
        $I->needPage(Url::to('@bill/create'));
        
        $page->fillMainBillFields(iterator_to_array($example->getIterator()));
        $I->pressButton('Save');
        $billId = $page->seeActionSuccess();
        
        $viewData['charge1'] = $page->addChargeInBillById($billId, $example['charge1']);
        $viewData['charge2'] = $page->addChargeInBillById($billId, $example['charge2']);
        $viewData['charge3'] = $page->addChargeInBillById($billId, $example['charge3']);
        
        $page->ViewBillById($billId);
        $indexPage->gridView->ensureChargeViewContainsData($viewData['charge1']);
        $indexPage->gridView->ensureChargeViewContainsData($viewData['charge2']);
        $indexPage->gridView->ensureChargeViewContainsData($viewData['charge3']);
    }

    private function getBillData(): array
    {
        return [
            'client' => [
                'login'     => 'hipanel_test_user',
                'type'      => 'Monthly fee',
                'currency'  => '$',
                'sum'       =>  250,
                'quantity'  =>  1,
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
        ];
    }
}
