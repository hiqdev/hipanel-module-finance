<?php

namespace hipanel\modules\finance\tests\acceptance\manager;

use Codeception\Example;
use hipanel\helpers\Url;
use hipanel\modules\finance\tests\_support\Page\plan\Create as PlanCreatePage;
use hipanel\modules\finance\tests\_support\Page\price\Create as PriceCreatePage;
use hipanel\modules\finance\tests\_support\Page\price\Update as PriceUpdatePage;
use hipanel\modules\finance\tests\_support\Page\price\Delete as PriceDeletePage;
use hipanel\tests\_support\Step\Acceptance\Manager;

class PriceCest
{
    /**
     * @var string
     */
    private $template = null;

    /**
     * @var string
     */
    private $id;

    /**
     * @dataProvider priceTypes
     * @param Manager $I
     * @param Example $example
     */
    public function ensureICanAddPricesTemplate(Manager $I, Example $example)
    {
        if (!$this->template) {
            $this->id = $this->createPlan($I, 'Template');
            $I->needPage(Url::to(['@plan/view', 'id' => $this->id]));
            $I->see('No prices found');
        }
        $page = new PriceCreatePage($I);
        $page->addPriceForTemplate($this->id, $example[0]);
    }

    private function priceTypes()
    {
        return [
            ['Model groups'],
            ['Dedicated Server'],
            ['vCDN'],
            ['pCDN']
        ];
    }

    /**
     * @dataProvider planTypes
     * @param Manager $I
     * @param Example $example
     */
    public function ensureICanAddPricesNonTemplate(Manager $I, Example $example)
    {
        $id = $this->createPlan($I, $example['type']);
        $I->needPage(Url::to(['@plan/view', 'id' => $id]));
        $I->see('No results found.');
        $page = new PriceCreatePage($I);
        foreach ($example['priceTypes'] as $priceType) {
            $page->addPriceForNonTemplate($id, $example['object'], $this->template, $priceType);
        }
    }

    private function planTypes()
    {
        return [
            [
                'type' => 'Server',
                'priceTypes' => ['Main prices', 'Parts prices'],
                'object' => 'DS5000',
            ],
            [
                'type' => 'vCDN',
                'priceTypes' => ['Main prices'],
                'object' => 'vCDN-soltest',
            ],
        ];
    }

    private function createPlan(Manager $I, $type)
    {
        $fields = [
            'name' => uniqid(),
            'type' => $type,
            'client' => 'hipanel_test_manager',
            'currency' => 'USD',
            'note' => 'test note',
        ];
        if ($type === 'Template') {
            $this->template = $fields['name'];
        }
        $page = new PlanCreatePage($I, $fields);
        return $page->createPlan();
    }

    public function ensureICanUpdatePrices(Manager $I)
    {
        $page = new PriceUpdatePage($I);
        $page->updatePrice($this->id);
    }

    public function ensureICanDeletePrices(Manager $I)
    {
        $page = new PriceDeletePage($I);
        $page->deleteTemplatePrices($this->id);
    }
}
