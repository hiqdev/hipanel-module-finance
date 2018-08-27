<?php

namespace hipanel\modules\finance\tests\acceptance\manager;

use hipanel\helpers\Url;
use hipanel\modules\finance\tests\_support\Page\plan\Create as PlanCreatePage;
use hipanel\modules\finance\tests\_support\Page\price\domain\Create as PriceDomainCreate;
use hipanel\modules\finance\tests\_support\Page\price\certificate\Update as PriceDomainUpdate;
use hipanel\tests\_support\Step\Acceptance\Manager;

class DomainCrudCest
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var array
     */
    private $fields;

    public function ensureThatICanCreateTariffPlan(Manager $I): void
    {
        $this->fields = [
            'name' => uniqid(),
            'type' => 'Domain tariff',
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
        $price = new PriceDomainCreate($I, $this->id);
        $price->addPrices('Default Tariff');
        $price->ensureThereNoSuggestions('Default Tariff');
    }

    public function ensureICanUpdatePrices(Manager $I)
    {
        $price = new PriceDomainUpdate($I, $this->id);
        $price->updatePrices();
    }
}
