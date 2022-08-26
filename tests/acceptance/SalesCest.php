<?php
declare(strict_types=1);

namespace hipanel\modules\finance\tests\acceptance;

use Codeception\Exception\ModuleException;
use Exception;
use hipanel\helpers\Url;
use Codeception\Example;
use hipanel\tests\_support\Page\IndexPage;
use hipanel\modules\finance\tests\_support\Page\sale\Edit;
use hipanel\modules\finance\tests\_support\Page\sale\Sale;
use hipanel\modules\finance\tests\_support\Page\sale\Index;
use hipanel\tests\_support\Page\Widget\Input\Select2;
use hipanel\tests\_support\Step\Acceptance\Seller;
use hipanel\tests\_support\Step\Acceptance\Manager;

class SalesCest
{
    /**
     * @dataProvider getSaleDataForManager
     * @throws ModuleException
     */
    public function ensureICanCreateSeveralSales(Manager $I, Example $example): void
    {
        $index = new IndexPage($I);
        $sale = new Sale($I);
        $saleData = iterator_to_array($example->getIterator());

        $I->needPage(Url::to('@server/index'));
        $I->waitForPageUpdate();
        $row = $index->getRowNumberInColumnByValue($saleData['column'], $saleData['server']);
        $index->openRowMenuByNumber($row);
        $index->chooseRowMenuOption('View');
        $I->clickLink('Change tariff');
        $I->waitForText('Affected items');

        $sale->fillSaleFields($saleData);

        $I->pressButton('Sell');
        $I->waitForPageUpdate();
        $I->closeNotification('Servers were sold');
    }

    /**
     * @dataProvider getSaleDataForSeller
     * @throws ModuleException
     * @throws Exception
     */
    public function ensureICanEditSeveralSales(Seller $I, Example $example): void
    {
        $I->login();
        $index = new Index($I);
        $edit = new Edit($I);

        $saleData = iterator_to_array($example->getIterator());

        $this->searchForSales($I, $saleData);

        foreach ([1, 2] as $rowId) {
            $I->checkOption("//tbody/tr[$rowId]//input");
        }

        $I->pressButton('Edit');

        $edit->fillSaleFields($saleData);
        $I->pressButton('Apply changes');
        $edit->seeActionSuccess();
    }

    public function ensureSaleDetailViewIsCorrect(Seller $I): void
    {
        $index = new Index($I);
        $edit = new Edit($I);

        $I->needPage(Url::to('@sale/index'));
        $column = $index->getColumnNumber('Time');

        $rowId = $index->getRowIdByNumber(1);

        $saleData = $I->grabMultiple("//tr[@data-key='$rowId']");

        unset($saleData[0], $saleData[2]);

        $value = $index->getValueFromCell($column, 1);
        $I->click("//tr[1]//td[$column]//a[contains(text(), '$value')]");
        $I->waitForPageUpdate();

        $edit->checkDetailViewData($saleData);
    }

    /**
     * @dataProvider getSaleDataForSeller
     * @throws ModuleException
     */
    public function ensureICanDeleteSeveralSales(Seller $I, Example $example): void
    {
        $index = new Index($I);
        $saleData = iterator_to_array($example->getIterator());

        $this->searchForSales($I, $saleData);

        foreach ([1, 2] as $rowId) {
            $I->checkOption("//tbody/tr[$rowId]//input");
        }

        $index->deleteSelectedSales();
    }

    /**
     * @throws ModuleException
     */
    private function searchForSales(Seller $I, array $saleData): void
    {
        $I->needPage(Url::to('@sale/index'));

        Select2::asAdvancedSearch($I, 'Tariff')->setValue($saleData['tariff']);
        $I->pressButton('Search');

        $I->waitForPageUpdate();
    }

    protected function getSaleDataForSeller(): array
    {
        return [
            'sale' => [
                'tariff' => 'PlanForkerViaLegacyApiTest / Plan to be clonned@hipanel_test_reseller',
            ],
        ];
    }

    protected function getSaleDataForManager(): array
    {
        return
            [
                [
                    'client' => 'hipanel_test_user2',
                    'tariff' => 'PlanForkerViaLegacyApiTest / Plan to be clonned@hipanel_test_reseller',
                    'column' => 'DC',
                    'server' => 'TEST-DS-01',
                ],
                [
                    'client' => 'hipanel_test_user2',
                    'tariff' => 'PlanForkerViaLegacyApiTest / Plan to be clonned@hipanel_test_reseller',
                    'column' => 'DC',
                    'server' => 'TEST-DS-02',
                ],
            ];
    }
}
