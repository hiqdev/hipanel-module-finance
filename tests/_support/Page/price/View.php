<?php

namespace hipanel\modules\finance\tests\_support\Page\price;

use hipanel\tests\_support\AcceptanceTester;
use hipanel\tests\_support\Page\Authenticated;
use hipanel\helpers\Url;

class View extends Authenticated
{
    /**
     * @var array
     */
    protected $priceValues = [];

    /**
     * @var int the target plan ID
     */
    private $id;

    public function __construct(AcceptanceTester $I, int $id)
    {
        parent::__construct($I);

        $this->id = $id;
        $this->loadPage();
    }

    protected function loadPage(): void
    {
        $I = $this->tester;

        $I->needPage(Url::to(['@plan/view', 'id' => $this->id]));
    }

    public function seeRandomPrices(): void
    {
        if (empty($this->priceValues)) {
            throw new \LogicException('Prices were not created yet');
        }

        $I = $this->tester;

        foreach ($this->priceValues as $value) {
            $I->seeInSource(number_format($value, 2));
        }
    }

    public function fillRandomPrices(string $type): void
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
