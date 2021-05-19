<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\tests\acceptance\client;

use hipanel\helpers\Url;
use hipanel\tests\_support\Step\Acceptance\Client;

class SalesCest
{
    public function ensureIndexPageWorks(Client $I)
    {
        $I->needPage(Url::to('@sale/index'));
        $I->see('Sales', 'h1');
        $this->ensureICanSeeAdvancedSearchBox($I);
        $this->ensureICanSeeBodyBox($I);
    }
    private function ensureICanSeeAdvancedSearchBox(Client $I)
    {
        $url = Url::to('@sale/index');
        $form = "//form[@action='$url']";
        $I->see('Advanced search', 'h3');
        $I->seeElement('input', ['id' => 'salesearch-object_inilike']);
        $I->see('Search', 'button');
    }
    private function ensureICanSeeBodyBox(Client $I)
    {
        $I->seeLink('Type');
        $I->seeElement('input', ['name' => 'SaleSearch[object_type]']);
    }
}
