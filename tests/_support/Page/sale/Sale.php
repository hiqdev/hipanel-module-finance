<?php
declare(strict_types=1);

namespace hipanel\modules\finance\tests\_support\Page\sale;

use hipanel\tests\_support\Page\Authenticated;
use hipanel\tests\_support\Page\Widget\Input\Select2;
use hipanel\tests\_support\Helper\PressButtonHelper;

class Sale extends Authenticated
{
    public function fillSaleFields(array $saleData): void
    {
        $I = $this->tester;

        (new Select2($I, "select[id*='server-client_id']"))->setValue($saleData['client']);

        (new Select2($I, "select[id*='server-tariff_id']"))->setValue($saleData['tariff']);
    }

    public function saveSell(): void
    {
        $this->tester->click("//form[@id='bulk-sale']//button[@id='save-button']");
    }
}
