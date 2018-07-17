<?php

namespace hipanel\modules\finance\tests\_support\Page\price\certificate;

use hipanel\modules\finance\tests\_support\Page\price\Create as PriceCreate;

class Create extends PriceCreate
{
    public function addPriceForCertificate(int $id, string $template): void
    {
        $I = $this->tester;

        $this->loadPage($id);
        $this->loadForm();
        $I->click('//div[contains(@class, "field-template_plan_id")]/span');
        $this->findTemplate($template);
        $this->proceedToCreation('Create prices');
        $this->fillRandomCertificatePrices();
        $this->savePrice();
        $this->seeRandomPrices();
    }

    protected function fillRandomCertificatePrices(): void
    {
        $I = $this->tester;

        $this->priceValues = $I->executeJS("
        var prices = [];
        $('table > tbody > tr').each(function(){
            var number = $(this).find('input[id^=certificateprice][id*=sums]');
            var randomValue = Math.floor(Math.random() * 2147483647);
            number.val(randomValue);
            prices.push(randomValue);
        });
        return prices;
        ");
    }
}
