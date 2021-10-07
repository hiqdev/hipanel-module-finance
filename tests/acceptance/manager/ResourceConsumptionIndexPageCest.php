<?php

namespace hipanel\modules\hosting\tests\acceptance\admin;

use hipanel\helpers\Url;
use hipanel\tests\_support\Page\IndexPage;
use hipanel\tests\_support\Page\Widget\Input\Select2;
use hipanel\tests\_support\Page\Widget\Input\Input;
use hipanel\tests\_support\Page\Widget\Input\Dropdown;
use hipanel\tests\_support\Step\Acceptance\Manager;

class ResourceConsumptionIndexPageCest
{
    private IndexPage $index;

    public function _before(Manager $I): void
    {
        $this->index = new IndexPage($I);
    }

    public function ensureIndexPageWorks(Manager $I): void
    {
        $I->login();
        $I->needPage(Url::to('@consumption/index'));
        $I->see('Resource consumption', 'h1');
        $this->ensureICanSeeAdvancedSearchBox($I);
        $this->ensureICanSeeBulkSearchBox();
    }

    private function ensureICanSeeAdvancedSearchBox(Manager $I): void
    {
        $this->index->containsFilters([
            Input::asAdvancedSearch($I, 'Name'),
            Dropdown::asAdvancedSearch($I, 'Object class'),
        ]);
    }

    private function ensureICanSeeBulkSearchBox(): void
    {
        $this->index->containsColumns([
            'object',
        ]);
    }
}
