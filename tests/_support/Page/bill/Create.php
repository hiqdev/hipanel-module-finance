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

    public function createBill(array $billData, $chargesData = []): void
    {
        $this->tester->needPage(Url::to('@bill/create'));
        $this->fillBillFields($billData, $chargesData);
        $this->save();
    }

    private function fillBillFields(array $billData, array $chargesData = []): void
    {
        $this->fillMainBillFields($billData);
        $this->addCharges($chargesData);
    }

    /**
     * @param array $billData
     */
    public function fillMainBillFields(array $billData): void
    {
        $I = $this->tester;

        $this->select2->open('#billform-0-client_id');
        $this->select2->fillSearchField($billData['login']);
        $this->select2->chooseOption($billData['login']);

        $I->selectOption('#billform-0-type', $billData['type']);

        $I->fillField(['billform-0-sum'], $billData['sum']);

        $I->click('//div[@class=\'input-group-btn\']/button[2]');
        $I->click('//li/a[contains(text(),\'$\')]');

        $I->fillField(['name' => 'BillForm[0][quantity]'], $billData['quantity']);
    }

    /**
     * @param int $sum
     */
    public function setBillTotalSum(int $sum): void
    {
        $this->tester->fillField(['billform-0-sum'], $sum);
    }

    public function addCharges(array $chargesData)
    {
        foreach ($chargesData as $chargeData) {
            $this->addCharge($chargeData);
        }
    }

    /**
     * @param array $chargeData
     */
    public function addCharge(array $chargeData): void
    {
        $this->clickAddChargeButton();
        if (!empty($chargeData)) {
            $this->fillChargeFields($chargeData);
        }
    }

    protected function clickAddChargeButton(): void
    {
        $this->tester->click('//div[@class=\'col-md-12 margin-bottom\']' .
            '/button[@type=\'button\']');
    }

    /**
     * @param array $chargeData
     */
    protected function fillChargeFields(array $chargeData): void
    {
        $I = $this->tester;

        $base = 'div.bill-charges>div:last-child ';
        $classSelector = $base . 'div[class=row] select[id$=class]';
        $I->selectOption($classSelector, $chargeData['class']);

        $objectIdSelector = $base . 'div[class=row] select[id$=object_id]';
        $this->select2->open($objectIdSelector);
        $this->select2->fillSearchField($chargeData['objectId']);
        $this->select2->chooseOption($chargeData['objectId']);

        $typeSelector = $base . 'div[class$=type] select';
        $I->selectOption($typeSelector, $chargeData['type']);

        $sumSelector = $base . 'div[class$=sum] input';
        $I->fillField($sumSelector, $chargeData['sum']);

        $qtySelector = $base . 'div[class$=quantity] input';
        $I->fillField($qtySelector, $chargeData['quantity']);
    }

    /**
     * Checks whether a page contains the specified quantity of charges
     *
     * @param int $n - quantity of charges
     */
    public function containsCharges(int $n): void
    {
        $selector = 'div.bill-charges div[class*=input-row]';
        $this->tester->seeNumberOfElements($selector, $n);
    }

    /**
     * Adds sum of each charge on page and returns it.
     *
     * @return int - total sum of charges
     */
    public function getChargesTotalSum(): int
    {
        $sum = $this->tester->executeJS(<<<JS
            var sum = 0;
            var selector = 'div.bill-charges div[class*=sum] input';
            var chargesSum = document.querySelectorAll(selector);
            chargesSum.forEach(function(chargeSum) {
               sum += parseInt(chargeSum.value); 
            });
            return sum
JS
        );
        return $sum;
    }

    public function deleteLastCharge()
    {
        $this->tester->click('div.bill-charges>div:last-child button');
    }

    /**
     * Checks whether a bill was created successfully and returns its id.
     *
     * @return string - id of created bill.
     */
    public function seeBillWasCreated(): string
    {
        $I = $this->tester;

        $I->closeNotification('Bill was created successfully');
        $I->seeInCurrentUrl('/finance/bill?id');

        return $I->grabFromCurrentUrl('~id_in%5B0%5D=(\d+)~');
    }

    /**
     * Looking for blank errors for the given fields.
     *
     * @param array $fieldsList
     * @throws \Exception
     */
    public function containsBlankFieldsError(array $fieldsList): void
    {
        foreach ($fieldsList as $field) {
            $this->tester->waitForText("$field cannot be blank.");
        }
    }

    /**
     * Looking for sum mismatch errors.
     *
     * @throws \Exception
     */
    public function containsSumMismatch(): void
    {
        $this->tester->waitForText('Bill sum must match charges sum:');
    }

    /**
     *  Saves created bill.
     */
    public function save(): void
    {
        $this->tester->click('//button[contains(@type,\'submit\')]');
    }
}
