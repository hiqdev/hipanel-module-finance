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
    protected $templateName;
    protected $planId;

    /**
     * @dataProvider getTemplateData
     */
    public function createTemplateTariffAndItsPrices(Seller $I,  Example $example): void
    {
        $I->login();
        $I->needPage(Url::to('@plan/create'));
        $exampleArray = iterator_to_array($example->getIterator());
        $this->planId = $this->ensureICanCreateNewTariff($I, $exampleArray);

        $this->createTemplatePrices($I, $exampleArray);
    }

    private function createTemplatePrices(Seller $I, array $templateData): void
    {
        $pricePage = new PriceCreate($I, $this->planId);

        $pricePage->createTemplatePrices($templateData['price']);
        $pricePage->saveForm();
    }

    /**
     * @dataProvider getTariffData
     */
    public function ensureICanCreateNewPlanAndCheckIt(Seller $I,  Example $example): void
    {
        $I->login();
        $I->needPage(Url::to('@plan/create'));
        $exampleArray = iterator_to_array($example->getIterator());
        $this->planId = $this->ensureICanCreateNewTariff($I, $exampleArray['plan']);
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
        $pricePage = new PriceCreate($I, $this->planId);

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
                    'type'     => 'AnycastCDN tariff',
                    'client'   => 'hipanel_test_reseller',
                    'currency' => 'EUR',
                    'note'     => 'note #' . uniqid(),
                    'typeDropDownElements' => [],
                ],
                'price' => [
                    'plan' => $this->templateName,
                    'type' => 'Main prices',
                ],
            ],
        ];
    }

    protected function getTemplateData(): array
    {
        return [
            'template' => [
                'name'     => $this->templateName  = 'template tariff' . uniqid(),
                'type'     => 'Template tariff',
                'client'   => 'hipanel_test_reseller',
                'currency' => 'EUR',
                'note'     => 'note #' . uniqid(),
                'typeDropDownElements' => [],
                'price'    => [
                    'type' => 'Anycast CDN',
                ],
            ],
        ];
    }
}
