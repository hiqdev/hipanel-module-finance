<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\tests\_support\Page\plan;

use hipanel\helpers\Url;
use hipanel\tests\_support\Page\Authenticated;

class Index extends Authenticated
{
    public function ensurePageWorks()
    {
        $I = $this->tester;

        $I->needPage(Url::to(['@plan']));
        $I->see('Tariff plans');
        $I->see('Advanced search');
        $I->seeLink('Create', Url::to(['@plan/create']));
        $I->seeElement('input', ['name' => 'PlanSearch[name_ilike]']);

        return $this;
    }

    public function ensurePlanCanBeFound($name)
    {
        $I = $this->tester;

        $I->fillField(['name' => 'PlanSearch[name_ilike]'], $name);
        $I->click('Search');
        $I->waitForJS("return $('tbody tr td a.bold').length === 1;", 60);
        $I->see($name);

        return $this;
    }

    public function ensurePlanNotFound($name)
    {
        $I = $this->tester;

        $I->fillField(['name' => 'PlanSearch[name_ilike]'], $name);
        $I->click('Search');
        $I->waitForJS("return $('tbody tr td a.bold').length === 0;", 60);
        $I->see('No results found.', '.empty');

        return $this;
    }
}
