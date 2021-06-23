<?php

namespace hipanel\modules\stock\tests\acceptance\seller;

use hipanel\helpers\Url;
use Codeception\Example;
use hipanel\tests\_support\Page\Authenticated;
use hipanel\tests\_support\Step\Acceptance\Seller;
use hipanel\tests\_support\Page\Widget\Input\Select2;
use hipanel\modules\finance\tests\_support\Page\plan\Create as PlanCreate;
use hipanel\modules\finance\tests\_support\Page\price\Create as PriceCreate;

class TariffPlansTipCest
{
    /**
     * @dataProvider getTariffData
     */
    public function ensureICanCreateNewPlanAndCheckIt(Seller $I,  Example $example): void
    {
        $I->login();
        $I->needPage(Url::to('@plan/create'));
        $exampleArray = iterator_to_array($example->getIterator());
        $exampleArray['price']['id'] = $this->ensureICanCreateNewTariff($I, $exampleArray['plan']);
        $this->ensureTipsAreCorrect($I, $exampleArray['price']);
    }

    private function ensureICanCreateNewTariff(Seller $I, array $tariffData): int
    {
        $planData = $this->getTariffData();
        $page = new PlanCreate($I, $tariffData, $planData['tariff']['plan']);
        return $page->createPlan();
    }

    private function ensureTipsAreCorrect(Seller $I, array $priceData): void
    {
        $pricePage = new PriceCreate($I, $priceData['id']);

        foreach ($pricePage->getCurrencyList() as $key => $currentCurrency) {
            $pricePage->lookForHelpTip($currentCurrency, $priceData);
        }
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
                    'typeDropDownElements' => [],
                ],
                'price' => [
                    'plan' => 'TEST-CONFIG-NL',
                    'type' => 'Main prices',
                ],
            ],
        ];
    }
}
