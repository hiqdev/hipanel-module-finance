<?php

namespace hipanel\modules\finance\tests\_support\Page\price\certificate;

use hipanel\modules\finance\tests\_support\Page\price\Create as PriceCreate;

class Create extends PriceCreate
{
    protected function fillRandomPrices(string $type = null): void
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

    public function addPrices(int $id, string $template): void
    {
        $I = $this->tester;

        $this->loadPage($id);
        $this->loadForm();
        $I->click('//div[contains(@class, "field-template_plan_id")]/span');
        $this->findTemplate($template);
        $this->proceedToCreation();
        $this->fillRandomPrices();
        $this->savePrice();
        $this->seeRandomPrices();
    }
}
