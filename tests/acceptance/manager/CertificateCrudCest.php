<?php

namespace hipanel\modules\finance\tests\acceptance\manager;

use hipanel\helpers\Url;
use hipanel\modules\finance\tests\_support\Page\plan\Create as PlanCreatePage;
use hipanel\modules\finance\tests\_support\Page\price\Create as PriceCreatePage;
use hipanel\modules\finance\tests\_support\Page\price\Update as PriceUpdatePage;
use hipanel\tests\_support\Step\Acceptance\Manager;

class CertificateCrudCest
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var array
     */
    private $fields;

    public function ensureThatICanCreateTariffPlan(Manager $I)
    {
        $this->fields = [
            'name' => uniqid(),
            'type' => 'Certificate tariff',
            'client' => 'hipanel_test_manager@hiqdev.com',
            'currency' => 'USD',
            'note' => 'test note',
        ];
        $page = new PlanCreatePage($I, $this->fields);
        $this->id = $page->createPlan();
    }

    public function ensureThatICanAddPrices(Manager $I)
    {
        $I->needPage(Url::to(['@plan/view', 'id' => $this->id]));
        $I->see('No prices found');
        $page = new PriceCreatePage($I);
        $page->addPriceForCertificate($this->id, 'Certificate tariff');
    }

    public function ensureICanUpdatePrices(Manager $I)
    {
        $page = new PriceUpdatePage($I);
        $page->updateCertificatePrice($this->id);
    }
}
