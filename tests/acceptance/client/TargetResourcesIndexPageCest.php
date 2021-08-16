<?php

namespace hipanel\modules\hosting\tests\acceptance\admin;

use hipanel\helpers\Url;
use hipanel\tests\_support\Page\IndexPage;
use hipanel\tests\_support\Page\Widget\Input\Select2;
use hipanel\tests\_support\Page\Widget\Input\Input;
use hipanel\tests\_support\Page\Widget\Input\Dropdown;
use hipanel\tests\_support\Step\Acceptance\Client;

class TargetResourcesIndexPageCest
{
    private IndexPage $index;

    public function _before(Client $I): void
    {
        $this->index = new IndexPage($I);
    }

    public function ensureIndexPageWorks(Client $I): void
    {
        $I->login();
        $I->needPage('finance/targetresource/index');
        $I->see('Target resources', 'h1');
        $this->ensureICanSeeAdvancedSearchBox($I);
        $this->ensureICanSeeBulkSearchBox();
    }

    private function ensureICanSeeAdvancedSearchBox(Client $I): void
    {
        $this->index->containsFilters([
            Input::asAdvancedSearch($I, 'Name'),
            Dropdown::asAdvancedSearch($I, 'Type'),
            Select2::asAdvancedSearch($I, 'Client'),
            Select2::asAdvancedSearch($I, 'Tariff Id'),
        ]);
    }

    private function ensureICanSeeBulkSearchBox(): void
    {
        $this->index->containsColumns([
            'Object',
            'SATA disk usage',
            'SSD',
            'Disk usage',
            'CDN Traffic OUT',
            'CDN Traffic',
            'Traffic OUT',
            'Traffic IN',
            '95 percentile traffic OUT',
            '95 percentile traffic IN',
        ]);
    }
}
