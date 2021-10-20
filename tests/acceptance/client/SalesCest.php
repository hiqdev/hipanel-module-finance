<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2021, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\tests\acceptance\client;

use hipanel\helpers\Url;
use hipanel\tests\_support\Page\IndexPage;
use hipanel\tests\_support\Page\Widget\Input\Input;
use hipanel\tests\_support\Step\Acceptance\Client;

class SalesCest
{
    private IndexPage $indexPage;

    public function _before(Client $I): void
    {
        $I->login();
        $I->needPage(Url::to('@sale/index'));
        $this->indexPage = new IndexPage($I);
    }

    public function ensureIndexPageWorks(Client $I): void
    {
        $I->login();
        $I->needPage(Url::to('@sale/index'));
        $I->see('Sales', 'h1');
        $this->ensureICanSeeAdvancedSearchBox($I);
        $this->ensureICanSeeBodyBox($I);
    }

    public function ensureFilterByObjectNameWorks(Client $I): void
    {
        $this->indexPage->filterBy(Input::asTableFilter($I, 'Object'), 'DSTEST01');
        $this->indexPage->containsAmountOfRows(1);
    }

    public function ensureFilterByTariffNameWorks(Client $I): void
    {
        $this->indexPage->filterBy(Input::asTableFilter($I, 'Tariff'), 'test dsr@dsr');
        $this->indexPage->containsAmountOfRows(1);
    }

    private function ensureICanSeeAdvancedSearchBox(Client $I): void
    {
        $I->see('Advanced search', 'h3');
        $I->seeElement('input', ['id' => 'salesearch-object_inilike']);
        $I->see('Search', 'button');
    }

    private function ensureICanSeeBodyBox(Client $I): void
    {
        $I->seeLink('Type');
        $I->seeElement('input', ['name' => 'SaleSearch[object_type]']);
    }
}
