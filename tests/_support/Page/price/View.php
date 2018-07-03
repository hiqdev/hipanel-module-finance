<?php

namespace hipanel\modules\finance\tests\_support\Page\price;

use hipanel\tests\_support\Page\Authenticated;
use hipanel\helpers\Url;

class View extends Authenticated
{
    /**
     * @var array
     */
    protected $priceValues;

    protected function loadPage($id)
    {
        $I = $this->tester;

        $I->needPage(Url::to(['@plan/view', 'id' => $id]));
    }

    protected function seeRandomPrices()
    {
        $I = $this->tester;

        foreach ($this->priceValues as $value) {
            $I->seeInSource(number_format($value, 2));
        }
    }

    protected function fillRandomPrices($type)
    {
        $I = $this->tester;

        $this->priceValues = $I->executeJS("
        var prices = [];
        $('.price-item').each(function(){
            var number = $(this).find('input[id^={$type}][id$=price]');
            var randomValue = Math.floor(Math.random() * 2147483647);
            number.val(randomValue);
            prices.push(randomValue);
        });
        return prices;
        ");
    }
}
