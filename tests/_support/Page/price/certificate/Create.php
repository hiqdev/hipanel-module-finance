<?php

namespace hipanel\modules\finance\tests\_support\Page\price\certificate;

use hipanel\modules\finance\tests\_support\Page\price\Create as PriceCreate;

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
        $this->select2->open('#template_plan_id');
        $this->select2->fillSearchField($templateName);
        $this->select2->chooseOption($templateName);
    }

    public function ensureThereNoSuggestions(string $templateName): void
    {
        $this->prepareForCreation($templateName);
        $this->seeNoSuggestions();
    }

    protected function seeNoSuggestions(): void
    {
        $I = $this->tester;

        $I->see("No price suggestions for this object");
        $I->see('We could not suggest any new prices of type "Certificate" for the selected object.');
        $I->see('Probably, they were already created earlier or this suggestion type is not compatible with this object type');
        $I->see("You can return back to plan");
    }
}
