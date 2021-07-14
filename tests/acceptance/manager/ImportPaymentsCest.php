<?php

namespace hipanel\modules\finance\tests\acceptance\manager;

use hipanel\helpers\Url;
use Codeception\Example;
use hipanel\modules\finance\tests\_support\Page\import\Create as ImportCreate;
use hipanel\modules\finance\tests\_support\Page\bill\Create as BillCreate;
use hipanel\modules\finance\tests\_support\Page\bill\View;
use hipanel\tests\_support\Step\Acceptance\Manager;

class ImportPaymentsCest
{
    /**
     * @dataProvider provideImportData
     */
    public function enusreImportPaymentsWorkCorrectly(Manager $I, Example $example): void
    {
        $I->login();
        $importData = iterator_to_array($example->getIterator()); 

        $I->needPage(Url::to('@bill'));
        $this->enusreImportFromAFileMethodWorksCorrectly($I);
        $this->enureImportPaymentsWorksCorrectly($I, $importData);
    }

    private function enusreImportFromAFileMethodWorksCorrectly(Manager $I): void
    {
        $I->clickLink('Import payments');
        $I->clickLink('Import from a file', '//ul');
        
        $this->ensureICantCreateImportedBillWithouData($I);

        $this->closeImportedBillPopup($I);
    }

    private function enureImportPaymentsWorksCorrectly(Manager $I, array $importData): void
    {
        $importCreate = new ImportCreate($I);
        $billCreate = new BillCreate($I);

        $I->clickLink('Import payments');
        $I->clickLink('Import payments', '//ul');

        $I->waitForPageUpdate();
        $I->seeInCurrentUrl('finance/bill/import');

        $I->see('Import payments', 'h1');
        $this->enusreImportTipIsCorrectlyDisplayed($I);

        $I->see('Rows for import', 'h3');
        $importCreate->fillImportField($importData);

        $I->pressButton('Import');
        $I->wait(3);
        $I->pressButton('Save');
        $billId = $billCreate->seeActionSuccess();

        $this->checkThatBillWasCreatedCorrectly($I, $importData, $billId);
    }

    private function checkThatBillWasCreatedCorrectly(Manager $I, array $importData, string $billId): void
    {
        $view = new View($I);

        $view->viewBillById($billId);
        $view->containsBillDataInTable($importData);
    }

    private function ensureICantCreateImportedBillWithouData(Manager $I): void
    {
        $create = new ImportCreate($I);
        $I->see("Create bills from file", 'h4');

        $I->pressButton('Create bills');
        $create->containsBlankFieldsError(['Requisite']);
        $I->see('File from the payment system cannot be blank.');
    }

    private function closeImportedBillPopup(Manager $I): void
    {
        $I->click("//div[@id = 'w1'] //button[@class = 'close']");
    }

    private function enusreImportTipIsCorrectlyDisplayed(Manager $I): void
    {
        $I->see('Use the following format:');
        $I->see('Client;Time;Amount;Currency;Type;Description;Requisite', 'pre');
    }

    protected function provideImportData(): array
    {
        date_default_timezone_set('Europe/Kiev');

        return [
            'importData' => [
                'client'      => 'hipanel_test_user',
                'time'        => date('h:i:s'),
                'amount'      => 300,
                'currency'    => 'USD',
                'type'        => 'PayPal',
                'description' => null,
            ],
        ];
    }
}
