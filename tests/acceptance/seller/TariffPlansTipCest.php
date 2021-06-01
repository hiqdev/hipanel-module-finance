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
        $id = $this->ensureICanCreateNewTariff($I, $example);
        $this->ensureTipsAreCorrect($I, $id);
    }

    private function ensureICanCreateNewTariff(Seller $I, $tariffData): int
    {
        $page = new Create($I);
        $I->needPage(Url::to('@plan/create'));
        return $page->createPlan($tariffData);
    }

    private function ensureTipsAreCorrect(Seller $I, $id): void
    {
        $page = new Create($I);
        $update = new Update($I);
        $currency = $this->getCurrencyData();
        $priceData = $this->getPriceData();

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
            'plan' => [
                'type'     => 'Server tariff',
                'currency' => 'USD',
            ],
        ];
    }

    protected function getPriceData(): array
    {
        return [
            'price' => [
                'plan' => 'TEST-CONFIG-NL',
                'type' => 'Main prices',
            ]
        ];
    }

    protected function getCurrencyData(): array
    {
        return [
            'usd' => 'USD',
            'eur' => 'EUR',
            'uah' => 'UAH',
            'rub' => 'RUB',
            'btc' => 'BTC',
        ];
    }
    
}
