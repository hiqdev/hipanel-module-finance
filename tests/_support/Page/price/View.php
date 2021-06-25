<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\tests\_support\Page\price;

use hipanel\helpers\Url;
use hipanel\tests\_support\AcceptanceTester;
use hipanel\tests\_support\Page\Authenticated;

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

    public function getId(): ?string
    {
        return $this->id;
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

        foreach ($this->priceValues as $values) {
            $I->seeInSource('$' . number_format($values['dollarValue'], 2));
            if (isset($values['euroValue'])) {
                $I->seeInSource('€' . number_format($values['euroValue'], 2));
            }
        }
    }

    public function fillRandomPrices(string $type): void
    {
        $I = $this->tester;

        $this->priceValues = $I->executeJS("
        var prices = [];
        $('.price-item').each(function(){
            var dollarInput = $(this).find('input[id*={$type}][id$=price]');
            var euroInput = $(this).find('input[id^=TemplatePrice][id*=subprices][id*=EUR]');
            var values = {'dollarValue': Math.floor(Math.random() * 2147483647)};
            dollarInput.val(values['dollarValue']);
            if (euroInput[0]) {
                values['euroValue'] = Math.floor(Math.random() * 2147483647);
                euroInput.val(values['euroValue']);
            }
            prices.push(values);
        });
        return prices;
        ");
    }
}
