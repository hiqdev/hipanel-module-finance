<?php

namespace hipanel\modules\finance\tests\acceptance\seller;

use hipanel\helpers\Url;
use hipanel\modules\finance\tests\_support\Page\bill\Create;
use hipanel\modules\finance\tests\_support\Page\bill\Update;
use hipanel\modules\finance\tests\_support\Page\bill\Index;
use hipanel\tests\_support\Page\Widget\Input\MultipleSelect2;
use hipanel\tests\_support\Helper\PressButtonHelper;
use hipanel\tests\_support\Step\Acceptance\Seller;

class BillCteationCest
{
    public $billId;
    private $billData;
    private Create $create;
    private Index $index;

    public function _before(Seller $I): void
    {
        $this->billdata = $this->getBillData();
        $this->create = new Create($I);
        $this->index = new Index($I);
    }

    public function ensureBillPageWorks(Seller $I): void
    {
        $I->login();
        $I->needPage(Url::to('@bill'));
    }

    /**
     * Tries to create a new simple bill without any data.
     *
     * Expects blank field errors.
     *
     * @param Seller $I
     * @throws \Exception
     */
    public function ensureICantCreateBillWithoutRequiredData(Seller $I): void
    {
        $I->needPage(Url::to('@bill/create'));

        $I->pressButton('Save');
        $this->create->containsBlankFieldsError(['Sum', 'Currency', 'Quantity']);
    }

    /**
     * Tries to create a new simple bill with all necessary data.
     *
     * Expects successful bill creation.
     *
     * @param Seller $I
     * @throws \Exception
     * 
     */
    public function ensureICanCreateSimpleBill(Seller $I): void
    {
        $I->needPage(Url::to('@bill/create'));

        $this->create->fillMainBillFields($this->billdata['bill1']);
        $I->pressButton('Save');

        $this->create->seeActionSuccess();
    }

    /**
     * Tries to create a new detailed bill without charge data.
     *
     * Expects blank field errors.
     *
     * @param Seller $I
     * @throws \Exception
     */
    public function ensureICantCreateDetailedBillWithoutData(Seller $I): void
    {
        $I->needPage(Url::to('@bill/create'));

        $this->create->fillMainBillFields($this->billdata['bill1']);
        $this->create->addCharge([]);
        $I->pressButton('Save');
        $I->waitForPageUpdate();
        $this->create->containsBlankFieldsError(['Sum', 'Qty.']);
        $this->create->deleteLastCharge();
    }

    /**
     * Tries to create a new detailed bill with all the necessary data.
     *
     * Expects successful bill creation.
     * Also checks Sum field mismatch error.
     *
     * @param Seller $I
     * @throws \Exception
     */
    public function ensureICanCreateDetailedBill(Seller $I): void
    {
        $I->needPage(Url::to('@bill/create'));

        $this->create->fillMainBillFields($this->billdata['bill2']);
        $this->create->addCharges([
            $this->getChargeData('hipanel_test_user1'),
            $this->getChargeData('hipanel_test_user2'),
        ]);
        $this->create->containsCharges(2);

        $I->pressButton('Save');

        $this->create->containsSumMismatch();

        $chargesSum = $this->create->getChargesTotalSum();

        $this->create->setBillTotalSum(-$chargesSum);
        $I->pressButton('Save');
        $this->billId = $this->create->seeActionSuccess();
    }

    /**
     * Tries to update early created bill.
     *
     * @param Seller $I
     * @throws \Codeception\Exception\ModuleException
     */
    public function ensureICanUpdateBill(Seller $I): void
    {
        $updatePage = new Update($I);

        $I->needPage(Url::to('@bill/index'));

        $this->index->sortBy('Time');
        $this->index->sortBy('Time');

        $this->index->openRowMenuById($this->billId);
        $this->index->chooseRowMenuOption('Update');

        $updatePage->containsCharges(2);

        $updatePage->deleteChargeByName('hipanel_test_user1');
        $updatePage->containsCharges(1);

        $chargesSum = $updatePage->getChargesTotalSum();
        $updatePage->setBillTotalSum(-$chargesSum);

        $I->pressButton('Save');
        $updatePage->seeActionSuccess();
    }

    /**
     * Checks whether a bill was updated successfully.
     *
     * @param Seller $I
     * @throws \Exception
     */
    public function ensureBillWasSuccessfullyUpdated(Seller $I): void
    {
        $updatePage = new Update($I);

        $I->needPage(Url::to('@bill/index'));

        $this->index->openRowMenuById($this->billId);
        $this->index->chooseRowMenuOption('Update');

        $updatePage->containsCharges(1);

        $chargeSelector = 'div.bill-charges:first-child';
        $I->see('hipanel_test_user2', $chargeSelector);
        $I->see('Server', $chargeSelector);
        $I->see('Monthly fee', $chargeSelector);
    }

    public function ensureAdvansedFilterWorksCorrectly(Seller $I): void
    {
        $I->needPage(Url::to('@bill/index'));

        $this->index->setAdvancedFilter(MultipleSelect2::asAdvancedSearch($I, 'Type'), 'Refund');
        $I->pressButton('Search');
        $I->waitForPageUpdate();

        $this->index->checkBillDataInBulkTable([$this->billdata['bill1'], $this->billdata['bill2']]);
    }

    protected function getBillData(): array
    {
        return [
            'bill1' => [
                'login'     => 'hipanel_test_user',
                'type'      => 'Uninstall',
                'currency'  => '$',
                'sum'       =>  750,
                'quantity'  =>  1,
            ],
            'bill2' => [
                'login'     => 'hipanel_test_user',
                'type'      => 'Unsale',
                'currency'  => '$',
                'sum'       =>  250,
                'quantity'  =>  1,
            ]
        ];
    }

    /**
     * @param string $objectId
     * @return array
     */
    protected function getChargeData(string $objectId): array
    {
        return [
            'class'     => 'Client',
            'objectId'  => $objectId,
            'type'      => 'Monthly fee',
            'sum'       => -250,
            'quantity'  => 1,
        ];
    }
}
