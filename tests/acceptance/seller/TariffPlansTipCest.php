<?php

namespace hipanel\modules\stock\tests\acceptance\seller;

use hipanel\helpers\Url;
use Codeception\Example;
use hipanel\tests\_support\Page\IndexPage;
use hipanel\tests\_support\Step\Acceptance\Seller;
use hipanel\tests\_support\Page\Widget\Input\Input;
use hipanel\tests\_support\Page\Widget\Input\Select2;
use hipanel\modules\finance\tests\_support\Page\plan\Create as planCreate;
use hipanel\modules\finance\tests\_support\Page\price\Create as priceCreate;

class TariffPlansTipCest
{
    /**
     * @dataProvider getTariffData
     */
    public function ensureIndexPageWorks(Seller $I,  Example $example): void
    {
        $I->login();
        $I->needPage(Url::to('@plan/create'));
        $id = $this->ensureICanCreateNewTariff($I, $example['plan']);
        $this->ensureTipsAreCorrect($I, $id, $example['price']);
    }

    private function ensureICanCreateNewTariff(Seller $I, array $tariffData): int
    {
        $page = new planCreate($I, $tariffData);
        return $page->createPlan();

    }

    private function ensureTipsAreCorrect(Seller $I, string $id, array $priceData): void
    {
        $pricePage = new priceCreate($I, 0);
        $planPage = new planCreate($I);
        $currency = $planPage->getCurrencyList();

        foreach($currency as $key => $currentCurrency)
        {
            $this->updatePlanWithNewCurrency($I, $currentCurrency, $id);
            $I->waitForText('Create prices', 10);
            $pricePage->createSharedPrice($priceData);
            $I->waitForElement("div[class*='0'] button[class*='formula-help']");
            $I->click("div[class*='0'] button[class*='formula-help']");
            $I->waitForText($currentCurrency);
        }
    }

    private function updatePlanWithNewCurrency(Seller $I, string $currency, string $id): void
    {
        $I->needPage(Url::to('@plan/update?id='. $id));
        (new Select2($I, '#plan-currency'))
            ->setValueLike($currency);
        $I->click('Save');
    }
    
    protected function getTariffData(): array
    {
        return [
            'tariff' => [
                'plan' => [
                    'name'     => uniqid(),
                    'type'     => 'Server tariff',
                    'client'   => 'hipanel_test_reseller',
                    'currency' => 'USD',
                    'note'     => 'note #' . uniqid(),
                ],
                'price' => [
                    'plan' => 'TEST-CONFIG-NL',
                    'type' => 'Main prices',
                ],
            ],
        ];
    }
}
