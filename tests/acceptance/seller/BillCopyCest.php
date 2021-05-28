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
     * @var IndexPage
     */
    private $index;

    public function _before(Seller $I)
    {
        $this->index = new IndexPage($I);
    }

    public function ensureICanCreateAndCopyBill(Seller $I)
    {
        $I->login();
        $dataBill = $this->provideDataBill();
        $billId = $this->ensureICanCreateSimpleBill($I, $dataBill['client']);
        $this->ensureICanCopyBill($I, $billId);
        $billId = $this->ensureICanCreateBillWithCharge($I, $dataBill['client']);
        $copyId = $this->ensureICanCopyBill($I, $billId);
        $billSum = $this->ensureICanEditCopiedBill($I, $copyId, $dataBill['client']);
        $checkId = $this->ensureICanSaveUpdatedBill($I);
        $this->ensureBillWasCreatedCorrectly($I, $checkId, $dataBill['client'], $billSum);
        $this->ensureChargesWasCreatedCorrectly($I, $checkId, $dataBill['client']);
        $this->ensurePreviousBillDidntChange($I, $billId, $dataBill['client']);
    }

    private function ensureICanCreateSimpleBill(Seller $I, $billData)
    {
        $page = new Create($I);
        $I->needPage(Url::to('@bill/create'));
        $page->fillMainBillFields($billData);
        $I->pressButton('Save');
        return $page->seeActionSuccess();
    }

    private function ensureICanCreateBillWithCharge(Seller $I, $billData)
    {
        $createPage = new Create($I);
        $I->needPage(Url::to('@bill/create'));
        $createPage->fillMainBillFields($billData);
        $createPage->addCharge($billData['charge1']);
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
        $sum = $pageCopy->getChargesTotalSum();
        $pageCopy->setBillTotalSum(-$sum);
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
        $indexPage = new IndexPage($I);
        $I->needPage(Url::to('@bill/view?id=' . $id));
        $tempResult = array_intersect_key($dataBill, array_flip(['login', 'type']));
        $tempResult[] = -$sum;
        $indexPage->gridView->ensureBillViewContainsData($tempResult);
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

    protected function provideDataBill(): array
    {
        return [
            'client' => [
                'login'     => 'hipanel_test_user',
                'type'      => 'HDD',
                'currency'  => '$',
                'sum'       =>  -44,
                'quantity'  =>  1,
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
        ];
    }
}
