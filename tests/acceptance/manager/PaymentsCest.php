<?php

namespace hipanel\modules\finance\tests\acceptance\manager;

use hipanel\helpers\Url;
use hipanel\modules\finance\tests\_support\Page\bill\Create;
use hipanel\modules\finance\tests\_support\Page\bill\Update;
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

        $page->save();
        $page->containsBlankFieldsError(['Client', 'Sum', 'Currency', 'Quantity']);
    }

    /**
     * Tries to create a new simple bill with all necessary data.
     *
     * Expects successful bill creation.
     *
     * @param Manager $I
     */
    public function ensureICanCreateSimpleBill(Manager $I): void
    {
        $page = new Create($I);
        $page->createBill($this->getBillData());

        $page->seeBillWasCreated();
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
        $page->save();
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
        $I->needPage(Url::to('@bill/create'));

        $page = new Create($I);
        $page->deleteLastCharge();

        $page->addCharges([
            $this->getChargeData('TEST01'),
            $this->getChargeData('vCDN-soltest')
        ]);
        $page->containsCharges(2);

        $page->save();
        $page->containsSumMismatch();

        $chargesSum = $page->getChargesTotalSum();

        $page->setBillTotalSum(-$chargesSum);
        $page->save();
        $this->billId = $page->seeBillWasCreated();
    }

    /**
     * Tries to update early created bill.
     *
     * @param Manager $I
     */
    public function ensureICanUpdateBill(Manager $I): void
    {
        $page = new Update($I);

        $page->goToUpdatePage($this->billId);
        $page->containsCharges(2);

        $page->deleteLastCharge();
        $page->containsCharges(1);

        $chargesSum = $page->getChargesTotalSum();
        $page->setBillTotalSum(-$chargesSum);

        $page->save();
        $page->seeBillWasUpdated();
    }

    /**
     * Checks whether a bill was updated successfully.
     *
     * @param Manager $I
     * @throws \Exception
     */
    public function ensureBillWasSuccessfullyUpdated (Manager $I): void
    {
        $page = new Update($I);

        $page->goToUpdatePage($this->billId);
        $page->containsCharges(1);

        $I->waitForElement('span[title=vCDN-soltest]', 5);
        $I->see('Server', 'div.bill-charges:first-child');
        $I->see('Monthly fee', 'div.bill-charges:first-child');
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
