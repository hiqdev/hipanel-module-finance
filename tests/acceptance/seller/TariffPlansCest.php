<?php

namespace hipanel\modules\stock\tests\acceptance\seller;

use hipanel\helpers\Url;
use hipanel\modules\finance\tests\_support\Entity\TemplateTariff;
use hipanel\modules\finance\tests\_support\Entity\Tariff;
use Codeception\Example;
use hipanel\tests\_support\Page\Authenticated;
use hipanel\tests\_support\Step\Acceptance\Seller;
use hipanel\tests\_support\Page\Widget\Input\Select2;
use hipanel\modules\finance\tests\_support\Page\plan\Create as PlanCreate;
use hipanel\modules\finance\tests\_support\Page\price\Create as PriceCreate;

class TariffPlansCest
{
    private $tariff;

    /**
     * @dataProvider provideTariffData
     */
    public function createTemplateTariffAndItsPrices(Seller $I,  Example $example): void
    {
        $I->login();
        $I->needPage(Url::to('@plan/create'));
        $this->tariff = $example['this'];
        $this->tariff->setTemplateId(
            $this->ensureICanCreateNewTariff($I, $this->tariff->getTemplateData())
        );

        $this->createTemplatePrices($I);
    }

    private function ensureICanCreateNewTariff(Seller $I, array $tariffData): int
    {
        $page = new PlanCreate($I, $tariffData);
        return $page->createPlan();
    }

    private function createTemplatePrices(Seller $I): void
    {
        $pricePage = new PriceCreate($I, $this->tariff->getTemplateId());

        $pricePage->createTemplatePrices($this->tariff->getTemplatePrice());
        $pricePage->saveForm();
    }

    public function ensureICanCreateNewPlanAndCheckIt(Seller $I): void
    {
        $I->needPage(Url::to('@plan/create'));

        $this->tariff->setTariffId(
            $this->ensureICanCreateNewTariff($I, $this->tariff->getTariffData())
        );

        $this->ensureTipsAreCorrect($I);
    }

    private function ensureTipsAreCorrect(Seller $I): void
    {
        $pricePage = new PriceCreate($I, $this->tariff->getTariffId());

        foreach ($pricePage->getCurrencyList() as $key => $currentCurrency) {
            $pricePage->lookForHelpTip($currentCurrency, $this->tariff->getTariffPrice());
        }
    }

    protected function provideTariffData(): \Generator
    {   
        yield [
                'this' => new Tariff(
                [
                    'name'     => uniqid(),
                    'type'     => 'AnycastCDN tariff',
                    'client'   => 'hipanel_test_reseller',
                    'currency' => 'EUR',
                    'note'     => 'note #' . uniqid(),
                    'typeDropDownElements' => [],
                    'price' => [
                        'plan' => '',
                        'type' => 'Main prices',
                    ],
                ],
                new TemplateTariff(
                    [
                        'name'     => 'template tariff' . uniqid(),
                        'type'     => 'Template tariff',
                        'client'   => 'hipanel_test_reseller',
                        'currency' => 'EUR',
                        'note'     => 'note #' . uniqid(),
                        'typeDropDownElements' => [],
                        'price'    => [
                            'type' => 'Anycast CDN',
                        ],
                    ],
                )
            )
        ];
    }
}
