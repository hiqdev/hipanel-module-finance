<?php
declare(strict_types=1);

namespace hipanel\modules\finance\tests\acceptance\seller;

use hipanel\helpers\Url;
use Codeception\Example;
use hipanel\tests\_support\Page\IndexPage;
use hipanel\modules\finance\tests\_support\Page\sale\Edit;
use hipanel\tests\_support\Page\Widget\Input\Select2;
use hipanel\tests\_support\Step\Acceptance\Seller;

class SalesIndexPageCest
{
    /**
     * @dataProvider getSaleData
     */
    public function EnsureICanEditSeveralSales(Seller $I, Example $example): void
    {
        $I->login();
        $edit = new Edit($I);
        $saleData = iterator_to_array($example->getIterator());

        $I->needPage(Url::to('@sale/index'));
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

        $saleData = $edit->editArrayForDetailView($I->grabMultiple("//tbody/tr[1]"), $column);

        $I->click("//tbody/tr[1]/td[$column]//a");
        $I->waitForPageUpdate();

        $edit->checkDetailViewData($saleData);
    }

    protected function getSaleData(): array
    {
        return [
            'sale' => [
                'tariff' => 'Sol Test 1@dsr'
            ],
        ];
    }
}
