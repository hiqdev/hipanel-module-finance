<?php
declare(strict_types=1);

namespace hipanel\modules\finance\tests\_support\Page\sale;

use hipanel\tests\_support\Page\Authenticated;
use hipanel\tests\_support\Page\Widget\Input\Select2;
use hipanel\tests\_support\Helper\PressButtonHelper;

class Edit extends Authenticated
{
    public function fillSaleFields(array $saleData): void
    {
        $I = $this->tester;

        $sales = $I->grabMultiple("select[id*=-tariff_id]");

        foreach ($sales as $n => $sale) {
            (new Select2($I, "select[id*='sale-$n-tariff_id']"))->setValue($saleData['tariff']);
        }
    }

    public function seeActionSuccess(): void
    {
        $this->tester->closeNotification('Sale has been successfully changed');
    }

    public function checkDetailViewData(array $salesData): void
    {
        $I = $this->tester;

        foreach ($salesData as $sale) {
            $I->see($sale, "//div[@class='box box-widget']");
        }
    }
}
