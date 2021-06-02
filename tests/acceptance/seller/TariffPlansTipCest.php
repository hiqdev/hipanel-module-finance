<?php

namespace hipanel\modules\stock\tests\acceptance\seller;

use hipanel\helpers\Url;
use Codeception\Example;
use hipanel\tests\_support\Page\IndexPage;
use hipanel\tests\_support\Step\Acceptance\Seller;
use hipanel\tests\_support\Page\Widget\Input\Input;
use hipanel\tests\_support\Page\Widget\Input\Select2;
use hipanel\modules\finance\tests\_support\Page\plan\Create;
use hipanel\modules\finance\tests\_support\Page\plan\Update;

class TariffPlansTipCest
{
    /**
     * @dataProvider getTariffData
     */
    public function ensureIndexPageWorks(Seller $I,  Example $example): void
    {
        $I->login();
        $id = $this->ensureICanCreateNewTariff($I, $example['plan']);
        $this->ensureTipsAreCorrect($I, $id, $example);
    }

    private function ensureICanCreateNewTariff(Seller $I, $tariffData): int
    {
        $page = new Create($I);
        return $page->createPlan($tariffData);
    }

    private function ensureTipsAreCorrect(Seller $I, $id, $priceData): void
    {
        $page = new Create($I);
        $update = new Update($I);
        $currency = $page->getCurrencyList();

        foreach($currency as $key => $currentCurrency)
        {
            $update->updatePlanWithNewCurrency($currentCurrency, $id);
            $I->waitForText('Create prices', 10);
            $page->createSharedPrice($priceData['price']);
            $I->waitForElement("div[class*='0'] button[class*='formula-help']");
            $I->click("div[class*='0'] button[class*='formula-help']");
            $I->waitForText($currentCurrency);
        }
    }
    
    protected function getTariffData(): array
    {
        return [
            'tariff' => [
                'plan' => [
                    'type'     => 'Server tariff',
                    'currency' => 'USD',
                ],
                'price' => [
                    'plan' => 'TEST-CONFIG-NL',
                    'type' => 'Main prices',
                ],
            ],
        ];
    }
}
