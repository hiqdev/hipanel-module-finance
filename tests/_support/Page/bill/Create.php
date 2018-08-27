<?php

namespace hipanel\modules\finance\tests\_support\Page\bill;

use hipanel\helpers\Url;
use hipanel\tests\_support\AcceptanceTester;
use hipanel\tests\_support\Page\Authenticated;
use hipanel\tests\_support\Page\Widget\Select2;

class Create extends Authenticated
{
    protected $select2;

    public function __construct(AcceptanceTester $I)
    {
        parent::__construct($I);

        $this->select2 = new Select2($I);
    }

    /**
     * Tries to create a new simple bill without any data.
     *
     * Expects blank field errors.
     * @throws \Exception
     */
    public function createBillWithoutData(): void
    {
        $I = $this->tester;

        $I->needPage(Url::to('@bill/create'));
        $this->clickSaveButton();

        $this->seeBlankFieldsError(['Client', 'Sum', 'Currency', 'Quantity']);
    }

    /**
     * Tries to create a new simple bill with all necessary data.
     *
     * Expects successful bill creation.
     *
     * @param array $billData
     */
    public function createBill(array $billData): void
    {
        $I = $this->tester;

        $I->needPage(Url::to('@bill/create'));
        $this->fillBillFields($billData);
        $this->clickSaveButton();
        $this->seeBillWasCreated();
    }

    /**
     * Tries to create a new detailed bill without charge data.
     *
     * Expects blank field errors.
     *
     * @param array $billData
     * @throws \Exception
     */
    public function createDetailedBillWithoutChargeData(array $billData): void
    {
        $I = $this->tester;

        $I->needPage(Url::to('@bill/create'));
        $this->fillBillFields($billData);
        $this->clickDetailingButton();
        $this->clickSaveButton();
        $this->seeBlankFieldsError(['Object', 'Sum', 'Quantity']);
    }

    /**
     * Tries to create a new detailed bill with all the necessary data.
     *
     * Expects successful bill creation.
     * Also checks Sum field mismatch error.
     *
     * @param array $billData
     * @throws \Exception
     */
    public function createDetailedBill(array $billData): void
    {
        $I = $this->tester;

        $I->needPage(Url::to('@bill/create'));

        $this->fillChargeFields($billData, 1);
        $this->clickDetailingButton();
        $this->fillChargeFields($billData, 2);
        $this->clickSaveButton();

        $I->waitForText('Bill sum must match charges sum:');
        $I->fillField(['name' => 'Charge[0][1][sum]'], -$billData['sum'] / 2);
        $I->fillField(['name' => 'Charge[0][2][sum]'], -$billData['sum'] / 2);

        $this->clickSaveButton();
        $this->seeBillWasCreated();
    }

    public function updateBill(): void
    {
        $I = $this->tester;

        $I->click('.//tr[1]/td/div/button');
        $I->click('//a[contains(text(),\'Update\')]');

        $I->seeInCurrentUrl('finance/bill/update?id');

        $I->fillField('#billform-0-sum', 250);
        $I->fillField('#charge-0-0-sum', -250);


        $I->click('//div[@class=\'bill-charges\']/div[3]/div[1]/div[2]/button[1]');

        $this->clickSaveButton();

        $this->seeBillWasUpdated();
    }

    /**
     * Checks whether a bill was updated successfully.
     */
    public function checkUpdatedBill(): void
    {
        $I = $this->tester;

        $I->click('.//tr[1]/td/div/button');
        $I->click('//a[contains(text(),\'Update\')]');

        $I->seeOptionIsSelected('#charge-0-0-class', 'Server');
        $I->seeOptionIsSelected('#charge-0-0-type', 'Monthly fee');

        $I->seeElement('span', ['title' => 'TEST02']);

        $I->dontSeeElement('#charge-0-1-class');
        $I->dontSeeElement('#charge-0-1-type');
        $I->dontSeeElement('span', ['title' => 'TEST01']);
    }

    /**
     * Fills basic bill fields.
     *
     * @param array $billData
     */
    protected function fillBillFields(array $billData): void
    {
        $I = $this->tester;

        $this->select2->open('#billform-0-client_id');
        $this->select2->fillSearchField($billData['login']);
        $this->select2->chooseOption($billData['login']);

        $I->selectOption('#billform-0-type', ['value' => $billData['type']]);

        $I->fillField(['billform-0-sum'], $billData['sum']);

        $I->click('//div[@class=\'input-group-btn\']/button[2]');
        $I->click('//li/a[contains(text(),\'$\')]');

        $I->fillField(['name' => 'BillForm[0][quantity]'], $billData['quantity']);
    }

    /**
     * Fills charge fields.
     *
     * @param array $billData
     * @param int $n number of charge block
     */
    protected function fillChargeFields(array $billData, $n): void
    {
        $I = $this->tester;

        $I->selectOption("#charge-0-$n-class", ['value' => 'Server']);

        $this->select2->open("#charge-0-$n-object_id");
        $this->select2->fillSearchField("TEST0$n");
        $this->select2->chooseOption("TEST0$n");

        $I->selectOption("#charge-0-$n-type", ['value' => $billData['type']]);

        $I->fillField(['name' => "Charge[0][$n][sum]"], $billData['sum']);

        $I->fillField(['name' => "Charge[0][$n][quantity]"], $billData['quantity']);
    }

    protected function clickSaveButton(): void
    {
        $this->tester->click('//button[contains(@type,\'submit\')]');
    }

    protected function clickDetailingButton(): void
    {
        $this->tester->click('//div[@class=\'col-md-12 margin-bottom\']' .
                                '/button[@type=\'button\']');
    }

    /**
     * Checks whether a bill was created successfully.
     */
    protected function seeBillWasCreated(): void
    {
        $I = $this->tester;

        $I->closeNotification('Bill was created successfully');
        $I->seeInCurrentUrl('/finance/bill?id');
    }

    protected function seeBillWasUpdated(): void
    {
        $I = $this->tester;

        $I->closeNotification('Bill was updated successfully');
        $I->seeInCurrentUrl('/finance/bill?id');
    }

    /**
     * Looking for blank errors for the given fields.
     *
     * @param array $fieldsList
     * @throws \Exception
     */
    protected function seeBlankFieldsError(array $fieldsList): void
    {
        foreach ($fieldsList as $field) {
            $this->tester->waitForText("$field cannot be blank.");
        }
    }
}
