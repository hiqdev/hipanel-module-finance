<?php

namespace hipanel\modules\finance\tests\acceptance\manager;

use hipanel\helpers\Url;
use hipanel\modules\finance\tests\_support\Page\bill\Create;
use hipanel\modules\finance\tests\_support\Page\bill\Update;
use hipanel\tests\_support\Page\IndexPage;
use hipanel\tests\_support\Step\Acceptance\Manager;

class PaymentsCest
{
    public $billId;

    public function ensureBillPageWorks(Manager $I): void
    {
        $I->login();
        $I->needPage(Url::to('@bill'));
    }

    /**
     * Tries to create a new simple bill without any data.
     *
     * Expects blank field errors.
     *
     * @param Manager $I
     * @throws \Exception
     */
    public function ensureICantCreateBillWithoutRequiredData(Manager $I): void
    {
        $page = new Create($I);

        $I->needPage(Url::to('@bill/create'));

        $I->pressButton('Save');
        $page->containsBlankFieldsError(['Client', 'Sum', 'Currency', 'Quantity']);
    }

    /**
     * Tries to create a new simple bill with all necessary data.
     *
     * Expects successful bill creation.
     *
     * @param Manager $I
     * @throws \Exception
     */
    public function ensureICanCreateSimpleBill(Manager $I): void
    {
        $page = new Create($I);

        $page->fillMainBillFields($this->getBillData());
        $I->pressButton('Save');

        $page->seeActionSuccess();
    }

    /**
     * Tries to create a new detailed bill without charge data.
     *
     * Expects blank field errors.
     *
     * @param Manager $I
     * @throws \Exception
     */
    public function ensureICantCreateDetailedBillWithoutData(Manager $I): void
    {
        $page = new Create($I);

        $I->needPage(Url::to('@bill/create'));

        $page->fillMainBillFields($this->getBillData());
        $page->addCharge([]);
        $I->pressButton('Save');
        $page->containsBlankFieldsError(['Object', 'Sum', 'Quantity']);
    }

    /**
     * Tries to create a new detailed bill with all the necessary data.
     *
     * Expects successful bill creation.
     * Also checks Sum field mismatch error.
     *
     * @param Manager $I
     * @throws \Exception
     */
    public function ensureICanCreateDetailedBill(Manager $I): void
    {
        $page = new Create($I);

        $I->amOnPage(Url::to('@bill/create'));

        $page->fillMainBillFields($this->getBillData());
        $page->addCharges([
            $this->getChargeData('TEST01'),
            $this->getChargeData('vCDN-soltest')
        ]);
        $page->containsCharges(2);

        $I->pressButton('Save');

        $page->containsSumMismatch();

        $chargesSum = $page->getChargesTotalSum();

        $page->setBillTotalSum(-$chargesSum);
        $I->pressButton('Save');
//        $this->billId = $page->seeActionSuccess();
    }

    /**
     * Tries to update early created bill.
     *
     * @param Manager $I
     * @throws \Codeception\Exception\ModuleException
     */
    protected function ensureICanUpdateBill(Manager $I): void
    {
        $indexPage  = new IndexPage($I);
        $updatePage = new Update($I);

        $indexPage->openRowMenuById($this->billId);
        $indexPage->chooseRowMenuOption('Update');

        $updatePage->containsCharges(2);

        $updatePage->deleteLastCharge();
        $updatePage->containsCharges(1);

        $chargesSum = $updatePage->getChargesTotalSum();
        $updatePage->setBillTotalSum(-$chargesSum);

        $I->pressButton('Save');
        $updatePage->seeActionSuccess();
    }

    /**
     * Checks whether a bill was updated successfully.
     *
     * @param Manager $I
     * @throws \Exception
     */
    protected function ensureBillWasSuccessfullyUpdated (Manager $I): void
    {
        $indexPage  = new IndexPage($I);
        $updatePage = new Update($I);

        $indexPage->openRowMenuById($this->billId);
        $indexPage->chooseRowMenuOption('Update');

        $updatePage->containsCharges(1);

        $chargeSelector = 'div.bill-charges:first-child';
        $I->see('vCDN-soltest', $chargeSelector);
        $I->see('Server', $chargeSelector);
        $I->see('Monthly fee', $chargeSelector);
    }

    /**
     * @return array
     */
    protected function getBillData(): array
    {
        return [
            'login'     => 'hipanel_test_user@hiqdev.com',
            'type'      => 'Monthly fee',
            'currency'  => '$',
            'sum'       =>  1000,
            'quantity'  =>  1
        ];
    }

    /**
     * @param string $objectId
     * @return array
     */
    protected function getChargeData(string $objectId): array
    {
        return [
            'class'     => 'Server',
            'objectId'  => $objectId,
            'type'      => 'Monthly fee',
            'sum'       => -250,
            'quantity'  => 1
        ];
    }
}
