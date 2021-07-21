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
    public function enureImportPaymentsWorksCorrectly(Manager $I, Example $example): void
    {
        $importCreate = new ImportCreate($I);
        $billCreate = new BillCreate($I);
        $view = new View($I);

        $I->login();
        $importData = iterator_to_array($example->getIterator()); 

        $I->needPage(Url::to('@bill/import'));

        $I->see('Import payments', 'h1');
        $I->see('Rows for import', 'h3');

        $importCreate->enusreImportTipIsCorrectlyDisplayed();
        $importCreate->fillImportField($importData);

        $I->pressButton('Import');
        $I->waitForElement("//h1[contains(text(),'Create payment')]", 10);
        $I->pressButton('Save');

        $view->viewBillById($billCreate->seeActionSuccess());
        $view->containsBillDataInTable($importData);
    }

    public function ensureICantCreateImportedBillWithoutData(Manager $I): void
    {
        $create = new ImportCreate($I);

        $I->needPage(Url::to('@bill'));
        $I->clickLink('Import payments');
        $I->clickLink('Import from a file', '//ul');

        $this->seeImportFromFilePopup($I);
        $I->see("Create bills from file", 'h4');

        $I->pressButton('Create bills');
        $create->containsBlankFieldsError(['Requisite']);
        $I->see('File from the payment system cannot be blank.');
    }

    private function seeImportFromFilePopup(Manager $I): void
    {
        $I->waitForElementVisible("form[id*='w']");
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
