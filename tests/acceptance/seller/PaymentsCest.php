<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\tests\acceptance\manager;

use hipanel\helpers\Url;
use hipanel\modules\finance\tests\_support\Page\bill\Create;
use hipanel\modules\finance\tests\_support\Page\bill\Update;
use hipanel\tests\_support\Page\IndexPage;
use hipanel\tests\_support\Step\Acceptance\Seller;

class PaymentsCest
{
    public $billId;

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
        $page = new Create($I);

        $I->needPage(Url::to('@bill/create'));

        $I->pressButton('Save');
        $page->containsBlankFieldsError(['Sum', 'Currency', 'Quantity']);
    }

    /**
     * Tries to create a new simple bill with all necessary data.
     *
     * Expects successful bill creation.
     *
     * @param Seller $I
     * @throws \Exception
     */
    public function ensureICanCreateSimpleBill(Seller $I): void
    {
        $page = new Create($I);

        $I->needPage(Url::to('@bill/create'));

        $page->fillMainBillFields($this->getBillData());
        $I->pressButton('Save');

        $page->seeActionSuccess();
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
        $page = new Create($I);

        $I->needPage(Url::to('@bill/create'));

        $page->fillMainBillFields($this->getBillData());
        $page->addCharge([]);
        $I->pressButton('Save');
        $page->containsBlankFieldsError(['Sum', 'Quantity']);
        $page->deleteLastCharge();
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
        $page = new Create($I);

        $I->needPage(Url::to('@bill/create'));

        $page->fillMainBillFields($this->getBillData());
        $page->addCharges([
            $this->getChargeData('TEST-DS-01'),
            $this->getChargeData('TEST-DS-02'),
        ]);
        $page->containsCharges(2);

        $I->pressButton('Save');

        $page->containsSumMismatch();

        $chargesSum = $page->getChargesTotalSum();

        $page->setBillTotalSum(-$chargesSum);
        $I->pressButton('Save');
        $this->billId = $page->seeActionSuccess();
    }

    /**
     * Tries to update early created bill.
     *
     * @param Seller $I
     * @throws \Codeception\Exception\ModuleException
     */
    public function ensureICanUpdateBill(Seller $I): void
    {
        $indexPage  = new IndexPage($I);
        $updatePage = new Update($I);

        $I->needPage(Url::to('@bill/index'));

        $indexPage->sortBy('Time');
        $indexPage->sortBy('Time');

        $indexPage->openRowMenuById($this->billId);
        $indexPage->chooseRowMenuOption('Update');

        $updatePage->containsCharges(2);

        $updatePage->deleteChargeByName('TEST-DS-01');
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
        $indexPage  = new IndexPage($I);
        $updatePage = new Update($I);

        $I->needPage(Url::to('@bill/index'));

        $indexPage->openRowMenuById($this->billId);
        $indexPage->chooseRowMenuOption('Update');

        $updatePage->containsCharges(1);

        $chargeSelector = 'div.bill-charges:first-child';
        $I->see('TEST-DS-02', $chargeSelector);
        $I->see('Server', $chargeSelector);
        $I->see('Monthly fee', $chargeSelector);
    }

    /**
     * @return array
     */
    protected function getBillData(): array
    {
        return [
            'login'     => 'hipanel_test_user',
            'type'      => 'Monthly fee',
            'currency'  => '$',
            'sum'       =>  1000,
            'quantity'  =>  1,
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
            'quantity'  => 1,
        ];
    }
}
