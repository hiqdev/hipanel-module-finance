<?php

namespace hipanel\modules\finance\tests\acceptance\manager;


use hipanel\tests\_support\Step\Acceptance\Manager;

class PlanCest extends PriceCest
{
    /**
     * @param Manager $I
     * @return array of settings for future plan
     * ```php
     *  [
     *      'type' => 'Server', // Type of the future plan
     *      'templateName' => 'Main template',
     *      'priceTypes' => ['Main prices', 'Parts prices'],
     *      'object' => 'DS5000',
     *  ],
     * ```
     */
    protected function suggestedPricesOptionsProvider(Manager $I): array
    {
        return [
         'type' => 'Server', // Type of the future plan
         'templateName' => 'Main template',
         'priceTypes' => ['Main prices', 'Parts prices'],
         'object' => 'DS5000',
        ];
    }
}