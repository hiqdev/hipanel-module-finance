<?php

namespace hipanel\modules\stock\tests\acceptance\seller;

use hipanel\helpers\Url;
use Codeception\Example;
use hipanel\tests\_support\Page\Authenticated;
use hipanel\tests\_support\Step\Acceptance\Seller;
use hipanel\modules\finance\tests\_support\Entity\Tariff;
use hipanel\modules\finance\tests\_support\Entity\TemplateTariff;
use hipanel\modules\finance\tests\_support\Page\plan\Create as PlanCreate;
use hipanel\modules\finance\tests\_support\Page\price\Create as PriceCreate;

class TariffPlansCest
{
    private string $templateName;

    /**
     * @dataProvider provideTariffData
     */
    public function createTemplateTariffAndItsPrices(Seller $I,  Example $example): void
    {
        $I->login();
        $I->needPage(Url::to('@plan/create'));

        $temp = iterator_to_array($example->getIterator());
        $tariff = $temp['tariff'];
        $this->templateName = $tariff->template->getName();
        $tariff->template->setId(
            $this->ensureICanCreateNewTariff($I, $tariff->template->getData())
        );
        $this->createTemplatePrices($I, $tariff);
    }

    private function ensureICanCreateNewTariff(Seller $I, array $tariffData): int
    {
        $page = new PlanCreate($I, $tariffData);
        return $page->createPlan();
    }

    private function createTemplatePrices(Seller $I, Tariff $tariff): void
    {
        $pricePage = new PriceCreate($I, $tariff->template->getId());

        $pricePage->createTemplatePrices($tariff->template->getPrice());
        $pricePage->saveForm();
    }

    /**
     * @dataProvider provideTariffData
     */
    public function ensureICanCreateNewPlanAndCheckIt(Seller $I, Example $example): void
    {
        $temp = iterator_to_array($example->getIterator());
        $tariff = $temp['tariff'];
        $I->needPage(Url::to('@plan/create'));

        $tariff->setId(
            $this->ensureICanCreateNewTariff($I, $tariff->getData())
        );

        $this->ensureTipsAreCorrect($I, $tariff);
    }

    private function ensureTipsAreCorrect(Seller $I, Tariff $tariff): void
    {
        $pricePage = new PriceCreate($I, $tariff->getId());
        $tariff->setTemplateName($this->templateName);

        foreach ($pricePage->getCurrencyList() as $key => $currentCurrency) {
            $pricePage->lookForHelpTip($currentCurrency, $tariff->getPrice());
        }
    }

    protected function provideTariffData(): \Generator
    {   
        yield [
            'tariff' => new Tariff(
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
                    'template' => new TemplateTariff(
                        [
                            'name'     => 'template tariff' . uniqid(),
                            'type'     => 'Template',
                            'client'   => 'hipanel_test_reseller',
                            'currency' => 'EUR',
                            'note'     => 'note #' . uniqid(),
                            'typeDropDownElements' => [],
                            'price'    => [
                                'type' => 'Anycast CDN',
                            ],
                        ],
                    )
                ],
            )
        ];
    }
}
