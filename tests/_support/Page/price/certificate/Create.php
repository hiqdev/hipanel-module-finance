<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\tests\_support\Page\price\certificate;

use hipanel\modules\finance\tests\_support\Page\price\Create as PriceCreate;
use hipanel\tests\_support\Page\Widget\Input\Select2;
use hipanel\tests\_support\Step\Acceptance\Client;

class Create extends PriceCreate
{
    public function fillRandomPrices(string $type): void
    {
        $I = $this->tester;

        $this->priceValues = $I->executeJS("
        var prices = [];
        $('.price-input').each(function(){
            var randomValue = Math.floor(Math.random() * 2147483647);
            $(this).val(randomValue);
            prices.push(randomValue);
        });
        return prices;
        ");
    }

    public function addPrices(string $templateName): void
    {
        $this->prepareForCreation($templateName);
        $this->fillRandomPrices('');
        $this->saveForm();
        $this->seeRandomPrices();
    }

    private function prepareForCreation(string $templateName): void
    {
        $this->loadPage();
        $this->openModal();
        $this->chooseTemplate($templateName);
        $this->proceedToCreation();
    }

    public function chooseTemplate(string $templateName): void
    {
        (new Select2($this->tester, '#template_plan_id'))->setValue($templateName);
    }

    public function ensureThereNoSuggestions(string $templateName): void
    {
        $this->prepareForCreation($templateName);
        $this->seeNoSuggestions();
    }

    protected function seeNoSuggestions(): void
    {
        $I = $this->tester;

        $I->see('No price suggestions for this object');
        $I->see('We could not suggest any new prices of type "Certificate" for the selected object.');
        $I->see('Probably, they were already created earlier or this suggestion type is not compatible with this object type');
        $I->see('You can return back to plan');
    }

    public function seeRandomPrices(): void
    {
        if (empty($this->priceValues)) {
            throw new \LogicException('Prices were not created yet');
        }

        $I = $this->tester;
        foreach ($this->priceValues as $value) {
            $I->seeInSource('$' . number_format($value, 2));
        }
    }
}
