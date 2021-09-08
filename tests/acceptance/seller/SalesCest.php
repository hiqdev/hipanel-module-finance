<?php
declare(strict_types=1);

namespace hipanel\modules\finance\tests\acceptance\seller;

use hipanel\helpers\Url;
use Codeception\Example;
use hipanel\tests\_support\Page\IndexPage;
use hipanel\modules\finance\tests\_support\Page\sale\Edit;
use hipanel\modules\finance\tests\_support\Page\sale\Sale;
use hipanel\modules\finance\tests\_support\Page\sale\Index;
use hipanel\tests\_support\Page\Widget\Input\Select2;
use hipanel\tests\_support\Step\Acceptance\Seller;
use hipanel\tests\_support\Step\Acceptance\Manager;

class SalesIndexPageCest
{
    /**
     * @dataProvider getSaleDataForManager
     */
    public function EnsureICanCreateSeveralSales(Manager $I, Example $example): void
    {
        $index = new IndexPage($I);
        $sale = new Sale($I);
        $saleData = iterator_to_array($example->getIterator());

        $I->login();
        $I->needPage('/server/server');
        $I->waitForPageUpdate();

        #$column = $index->gridView->getColumnNumber('DC');
        $row[] = $index->getRowNumberInColumnByValue('DC', 'TEST-DS-01');
        $row[] = $index->getRowNumberInColumnByValue('DC', 'TEST-DS-02');

        foreach ($row as $currentRow) {
            $I->needPage('/server/server');
            $index->openRowMenuByNumber($currentRow);
            $index->chooseRowMenuOption('View');
            $I->clickLink('Change tariff');
            $I->waitForElementVisible("//div[contains(text(),'Affected items')]", 90);

            $sale->fillSaleFields($saleData);
            
            $I->pressButton('Sell');
            $I->waitForPageUpdate();
            $I->closeNotification('Servers were sold');
        }

        $I->logout($I);
    }

    /**
     * @dataProvider getSaleDataForSeller
     */
    public function ensureICanEditSeveralSales(Seller $I, Example $example): void
    {
        $index = new IndexPage($I);
        $edit = new Edit($I);

        $I->login();
        $saleData = iterator_to_array($example->getIterator());

        $I->needPage(Url::to('@sale/index'));

        $index->setAdvancedFilter(Select2::asAdvancedSearch($I, 'Tariff'), $saleData['tariff']);
        $I->pressButton('Search');

        $I->waitForPageUpdate();

        $I->checkOption("//tbody/tr[1]//input[1]");
        $I->checkOption("//tbody/tr[2]//input[1]");
        $I->pressButton('Edit');
        $edit->fillSaleFields($saleData);
    }

    private function fillSaleEditFields(Seller $I, array $saleData): void
    {
        $edit = new Edit($I);

        $edit->fillSaleFields($saleData);
        $I->pressButton('Apply changes');
        $edit->seeActionSuccess();
    }

    public function EnsureSaleDetailViewIsCorrect(Seller $I): void
    {
        $index = new IndexPage($I);
        $edit = new Edit($I);

        $I->needPage(Url::to('@sale/index'));
        $column = $index->gridView->getColumnNumber('Time');

        $saleData = $I->grabMultiple("//tbody/tr[1]");
        unset($saleData[0], $saleData[2]);

        $I->click("//tbody/tr[1]/td[$column]//a");
        $I->waitForPageUpdate();

        $edit->checkDetailViewData($saleData);
    }

    /**
     * @dataProvider getSaleDataForSeller
     */
    public function EnsureICanDeleteSeveralSales(Seller $I, Example $example): void
    {
        $index = new Index($I);
        $indexPage = new IndexPage($I);
        $saleData = iterator_to_array($example->getIterator());

        $I->needPage(Url::to('@sale/index'));

        $indexPage->setAdvancedFilter(Select2::asAdvancedSearch($I, 'Tariff'), $saleData['tariff']);
        $I->pressButton('Search');

        $I->waitForPageUpdate();

        $I->checkOption("//tbody/tr[1]//input[1]");
        $I->checkOption("//tbody/tr[2]//input[1]");

        $index->deleteSelectedSales();
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
        return [
            'sale' => [
                'client' => 'testuser60798ee837548@test1.test1',
                'tariff' => 'PlanForkerViaLegacyApiTest / Plan to be clonned@hipanel_test_reseller',
            ],
        ];
    }
}
