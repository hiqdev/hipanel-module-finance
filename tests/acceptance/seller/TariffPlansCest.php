<?php
declare(strict_types=1);

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
        $this->templateName = $tariff->template->name;
        $tariff->template->id = $this->ensureICanCreateNewTariff($I, get_object_vars($tariff->template));
        $this->createTemplatePrices($I, $tariff);
    }

    private function ensureICanCreateNewTariff(Seller $I, array $tariffData): int
    {
        $page = new PlanCreate($I, $tariffData);
        return $page->createPlan();
    }

    private function createTemplatePrices(Seller $I, Tariff $tariff): void
    {
        $pricePage = new PriceCreate($I, $tariff->template->id);

        $pricePage->createTemplatePrices($tariff->template->price);
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

        $tariff->id = $this->ensureICanCreateNewTariff($I, get_object_vars($tariff));

        $this->ensureTipsAreCorrect($I, $tariff);
    }

    private function ensureTipsAreCorrect(Seller $I, Tariff $tariff): void
    {
        $pricePage = new PriceCreate($I, $tariff->id);
        $tariff->price['plan'] = $this->templateName;

        foreach ($pricePage->getCurrencyList() as $key => $currentCurrency) {
            $pricePage->lookForHelpTip($currentCurrency, $tariff->price);
        }
    }

    protected function provideTariffData(): \Generator
    {   
        yield [
            'tariff' => new Tariff(
                'tariff' . uniqid(),
                'AnycastCDN tariff',
                'hipanel_test_reseller',
                'EUR',
                'note #' . uniqid(),
                [
                    'plan' => '',
                    'type' => 'Main prices',
                ],
                new TemplateTariff(
                    'template tariff' . uniqid(),
                    'Template',
                    'hipanel_test_reseller',
                    'EUR',
                    'note #' . uniqid(),
                    [
                        'type' => 'Anycast CDN',
                    ]
                )
            )
        ];
    }
}
