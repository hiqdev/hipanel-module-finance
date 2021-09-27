<?php
declare(strict_types=1);

namespace hipanel\modules\finance\tests\acceptance\seller;

use hipanel\helpers\Url;
use hipanel\tests\_support\Page\IndexPage;
use hipanel\tests\_support\Page\Widget\Input\Input;
use hipanel\tests\_support\Step\Acceptance\Seller;

class TariffProfilesIndexCest
{
    public function ensureIndexPageWorks(Seller $I): void
    {
        $I->login();
        $I->needPage(Url::to('@tariffprofile/index'));
        $I->see('Tariff profiles', 'h1');
        $this->ensureICanSeeAdvancedSearchBox($I);
        $this->ensureICanSeeBulkTariffProfilesSearchBox($I);
    }

    private function ensureICanSeeAdvancedSearchBox(Seller $I): void
    {
        $index = new IndexPage($I);

        $index->containsFilters([
            Input::asAdvancedSearch($I, 'Name'),
        ]);
    }

    private function ensureICanSeeBulkTariffProfilesSearchBox(Seller $I): void
    {
        $index = new IndexPage($I);

        $index->containsBulkButtons([
            'Delete',
        ]);
        $index->containsColumns([
            'Name',
            'Client',
            'Tariff',
        ]);
    }
}
