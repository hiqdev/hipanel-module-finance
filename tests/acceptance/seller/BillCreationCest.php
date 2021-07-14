<?php

namespace hipanel\modules\finance\tests\acceptance\seller;

use hipanel\helpers\Url;
use Codeception\Example;
use hipanel\modules\finance\tests\_support\Page\bill\Create;
use hipanel\modules\finance\tests\_support\Page\bill\Update;
use hipanel\modules\finance\tests\_support\Page\bill\Index;
use hipanel\tests\_support\Page\Widget\Input\MultipleSelect2;
use hipanel\tests\_support\Helper\PressButtonHelper;
use hipanel\tests\_support\Step\Acceptance\Seller;

class BillCteationCest
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
        $this->create = new Create($I);

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
     * @dataProvider getBillData
     */
    public function ensureICanCreateSimpleBill(Seller $I, Example $example): void
    {
        $create = new Create($I);
        $billData = iterator_to_array($example->getIterator());

        $I->needPage(Url::to('@bill/create'));

        $create->fillMainBillFields($billData);
        $I->pressButton('Save');

        $create->seeActionSuccess();
    }

    /**
     * Tries to create a new detailed bill without charge data.
     *
     * Expects blank field errors.
     *
     * @param Seller $I
     * @throws \Exception
     *
     * @dataProvider getBillData
     */
    public function ensureICantCreateDetailedBillWithoutData(Seller $I, Example $example): void
    {
        $create = new Create($I);
        $billData = iterator_to_array($example->getIterator());

        $I->needPage(Url::to('@bill/create'));

        $create->fillMainBillFields($billData);
        $create->addCharge([]);
        $I->pressButton('Save');
        $I->waitForPageUpdate();
        $create->containsBlankFieldsError(['Sum', 'Qty.']);
        $create->deleteLastCharge();
    }

    /**
     * Tries to create a new detailed bill with all the necessary data.
     *
     * Expects successful bill creation.
     * Also checks Sum field mismatch error.
     *
     * @param Seller $I
     * @throws \Exception
     *
     * @dataProvider getBillData
     */
    public function ensureICanCreateDetailedBill(Seller $I, Example $example): void
    {
        $create = new Create($I);
        $billData = iterator_to_array($example->getIterator());

        $I->needPage(Url::to('@bill/create'));

        $create->fillMainBillFields($billData);
        $create->addCharges([
            $this->getChargeData('hipanel_test_user1'),
            $this->getChargeData('hipanel_test_user2'),
        ]);
        $create->containsCharges(2);

        $I->pressButton('Save');

        $create->containsSumMismatch();

        $chargesSum = $create->getChargesTotalSum();

        $create->setBillTotalSum(-$chargesSum);
        $I->pressButton('Save');
        $this->billId = $create->seeActionSuccess();
    }

    /**
     * Tries to update early created bill.
     *
     * @param Seller $I
     * @throws \Codeception\Exception\ModuleException
     */
    public function ensureICanUpdateBill(Seller $I): void
    {
        $index = new Index($I);
        $update = new Update($I);

        $I->needPage(Url::to('@bill/index'));

        $index->sortBy('Time');
        $index->sortBy('Time');

        $index->openRowMenuById($this->billId);
        $index->chooseRowMenuOption('Update');

        $update->containsCharges(2);

        $update->deleteChargeByName('hipanel_test_user1');
        $update->containsCharges(1);

        $chargesSum = $update->getChargesTotalSum();
        $update->setBillTotalSum(-$chargesSum);

        $I->pressButton('Save');
        $update->seeActionSuccess();
    }

    /**
     * Checks whether a bill was updated successfully.
     *
     * @param Seller $I
     * @throws \Exception
     */
    public function ensureBillWasSuccessfullyUpdated(Seller $I): void
    {
        $index = new Index($I);
        $update = new Update($I);

        $I->needPage(Url::to('@bill/index'));

        $index->openRowMenuById($this->billId);
        $index->chooseRowMenuOption('Update');

        $update->containsCharges(1);

        $chargeSelector = 'div.bill-charges:first-child';
        $I->see('hipanel_test_user2', $chargeSelector);
        $I->see('Server', $chargeSelector);
        $I->see('Monthly fee', $chargeSelector);
    }

    /**
     * @dataProvider getBillData
     */
    public function ensureAdvansedFilterWorksCorrectly(Seller $I, Example $example): void
    {
        $index = new Index($I);
        $billData = iterator_to_array($example->getIterator());

        $I->needPage(Url::to('@bill/index'));

        $index->setAdvancedFilter(MultipleSelect2::asAdvancedSearch($I, 'Type'), 'Refund');
        $I->pressButton('Search');
        $I->waitForPageUpdate();

        $index->checkBillDataInBulkTable($billData);
    }

    protected function getBillData(): array
    {
        return [
            [
                'login'     => 'hipanel_test_user',
                'type'      => 'Uninstall',
                'currency'  => '$',
                'sum'       =>  750,
                'quantity'  =>  1,
            ],
            [
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
            'sum'       => -350,
            'quantity'  => 1,
        ];
    }
}
