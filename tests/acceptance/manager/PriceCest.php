<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\tests\acceptance\manager;

use Codeception\Scenario;
use hipanel\helpers\Url;
use hipanel\modules\finance\tests\_support\Page\plan\Create as PlanCreatePage;
use hipanel\modules\finance\tests\_support\Page\price\Create as PriceCreatePage;
use hipanel\modules\finance\tests\_support\Page\price\Delete as PriceDeletePage;
use hipanel\modules\finance\tests\_support\Page\price\Update as PriceUpdatePage;
use hipanel\tests\_support\Step\Acceptance\Manager;

abstract class PriceCest
{
    /**
     * @var int
     */
    protected $id;

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
    abstract protected function suggestedPricesOptionsProvider(Manager $I): array;

    /**
     * @param Manager $I
     */
    public function ensureICanCreateSuggestedPrices(Manager $I): void
    {
        foreach ($this->suggestedPricesOptionsProvider($I) as $example) {
            $I->amGoingTo(sprintf(
                'Create new plan with type "%s" and fill it with prices of template "%s" (suggestions %s) for object %s',
                $example['type'], $example['templateName'], implode(', ', $example['priceTypes']), $example['object']
            ));

            $id = $this->createPlan($I, uniqid($example['type'] . 'Plan_Of_' . $example['templateName'], true), $example['type']);
            $I->needPage(Url::to(['@plan/view', 'id' => $id]));
            $I->see('No results found.');

            $page = new PriceCreatePage($I, $id);
            foreach ($example['priceTypes'] as $priceType) {
                $page->createRandomPrices($example['object'], $example['templateName'], $priceType);
            }
        }
    }

    public function ensureICanUpdatePrices(Manager $I, Scenario $scenario): void
    {
        if ($this->id === null) {
            $scenario->incomplete('ID of the target plan must be set');
        }

        $page = new PriceUpdatePage($I, $this->id);
        $page->updatePrices();
    }

    public function ensureICanDeletePrices(Manager $I, Scenario $scenario): void
    {
        if ($this->id === null) {
            $scenario->incomplete('ID of the target plan must be set');
        }

        $page = new PriceDeletePage($I, $this->id);
        $page->deleteTemplatePrices();
    }

    /**
     * @param Manager $I
     * @param string $name
     * @param string $type
     * @return int created plan ID
     */
    protected function createPlan(Manager $I, string $name, string $type): int
    {
        $fields = [
            'name' => $name,
            'type' => $type,
            'client' => 'hipanel_test_reseller',
            'currency' => 'USD',
            'note' => 'test note',
        ];

        $page = new PlanCreatePage($I, $fields);

        return $page->createPlan();
    }
}
