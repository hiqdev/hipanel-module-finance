<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\tests\_support\Page\bill;

use hipanel\tests\_support\Page\Authenticated;
use hipanel\tests\_support\Page\Widget\Input\Dropdown;
use hipanel\tests\_support\Page\Widget\Input\Input;
use hipanel\tests\_support\Page\Widget\Input\Select2;
use hipanel\modules\finance\tests\_support\Page\bill\Copy;
use hipanel\helpers\Url;

class Create extends Authenticated
{
    /**
     * @param array $billData
     * @throws \Exception
     */
    public function fillMainBillFields(array $billData): void
    {
        $I = $this->tester;

        (new Select2($I, '#billform-0-client_id'))->setValue($billData['login']);
        (new Dropdown($I, '#billform-0-type'))->setValue($billData['type']);
        (new Input($I, '#billform-0-sum'))->setValue($billData['sum']);

        $I->click('//div[@class=\'input-group-btn\']/button[2]');
        $I->click('//li/a[contains(text(),\'$\')]');

        (new Input($I, '#billform-0-quantity'))->setValue($billData['quantity']);
    }

    /**
     * @param int $sum
     */
    public function setBillTotalSum(int $sum): void
    {
        $this->tester->fillField(['billform-0-sum'], $sum);
    }

    /**
     * @param array $chargesData
     * @throws \Exception
     */
    public function addCharges(array $chargesData)
    {
        foreach ($chargesData as $chargeData) {
            $this->addCharge($chargeData);
        }
    }

    /**
     * @param array $chargeData
     * @throws \Exception
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
        $this->tester->click('button.add-charge');
    }

    /**
     * @param array $chargeData
     * @throws \Exception
     */
    protected function fillChargeFields(array $chargeData): void
    {
        $I = $this->tester;

        $base = 'div.bill-charges>div:last-child ';

        (new Dropdown($I, $base . "select[id*=class]"))->setValue($chargeData['class']);

        $objectIdSelector = $base . 'div[class=row] select[id*=object_id]';
        (new Select2($I, $objectIdSelector))->setValue($chargeData['objectId']);

        $typeSelector = $base . 'div[class*=type] select';
        (new Dropdown($I, $typeSelector))->setValue($chargeData['type']);

        $sumSelector = $base . 'div[class*=sum] input';
        (new Input($I, $sumSelector))->setValue($chargeData['sum']);

        $qtySelector = $base . 'div[class*=quantity] input';
        (new Input($I, $qtySelector))->setValue($chargeData['quantity']);
    }

    /**
     * Checks whether a page contains the specified quantity of charges.
     *
     * @param int $n - quantity of charges
     */
    public function containsCharges(int $n): void
    {
        $selector = 'div.bill-charges div[class*=input-row]';
        $this->tester->seeNumberOfElements($selector, $n);
    }

    public function getChargesAmount(): array
    {
        $I = $this->tester;
        $selector = 'div.bill-charges div[class*=input-row]';

        return $I->grabMultiple($selector);        
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

    public function deleteLastCharge(): void
    {
        $this->tester->click('div.bill-charges>div:last-child button');
    }

    public function deleteChargeByName(string $chargeName): void
    {
        $this->tester->executeJS(
            ';let charge = $("div.bill-charges :contains(\'" + arguments[0] + "\')");
            charge.parents(".charge-item").find("button").click();',
            [$chargeName]
        );
    }
    public function deleteBillById($billId): void 
    {
        $I = $this->tester;
        $url = Url::to('@bill/view?id=' . $billId);
        $I->amOnPage($url);
        $I->see('Description');
        $I->see('Bill not paid');
        $I->click('//a[@data-confirm="Are you sure you want to delete this item?"]');
        $I->acceptPopup();
        $I->closeNotification('Payment was deleted successfully');
    }

    /**
     * Checks whether a bill was created successfully and returns its id.
     *
     * @return string - id of created bill
     */
    public function seeActionSuccess(): ?string
    {
        $I = $this->tester;

        $I->closeNotification('Bill was created successfully');
        $I->seeInCurrentUrl('/finance/bill?id');

        $id = $this->grabBillIdFromUrl();
        return $id;
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

    public function visit(): void
    {
        $this->tester->needPage(Url::to('@bill/create'));
    }

    public function clickToggleSign(): void 
    {
        $this->tester->click('Toggle sign');
    }

    private function grabBillIdFromUrl(): ?string
    {
        $I = $this->tester;
        return $I->grabFromCurrentUrl('~id_in%5B0%5D=(\d+)~');
    }

    public function getAndSetBillTotalSum(): int 
    {
        $sum = $this->getChargesTotalSum();
        $this->setBillTotalSum($this->getChargesTotalSum());
        
        return $sum;
    }
}
