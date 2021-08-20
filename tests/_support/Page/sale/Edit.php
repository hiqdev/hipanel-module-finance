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
        $n = 0;

        foreach ($sales as $sale) {
            (new Select2($I, "//select[@id='sale-$n-tariff_id']"))->setValue($saleData['tariff']);
            $n++;
        }
    }

    public function seeActionSuccess(): void
    {
        $this->tester->closeNotification('Sale has been successfully changed');
    }

    public function editArrayForDetailView(array $saleData, int $column): array
    {
        $I = $this->tester;

        unset($saleData[0]);
        unset($saleData[2]);

        $saleData[2] = $I->grabTextFrom("//tbody/tr[1]/td[3]//a");
        $saleData[] = str_replace($saleData[2], '', $I->grabTextFrom("//tbody/tr[1]/td[3]"));

        return $saleData;
    }

    public function checkDetailViewData(array $saleData): void
    {
        $I = $this->tester;

        foreach ($saleData as $data) {
            $I->see($data, "//div[@class='box box-widget']");
        }
    }
}
