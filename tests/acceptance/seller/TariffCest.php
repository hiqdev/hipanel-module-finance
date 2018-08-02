<?php

namespace hipanel\modules\finance\tests\acceptance\seller;

use hipanel\helpers\Url;
use hipanel\tests\_support\Page\IndexPage;
use hipanel\tests\_support\Page\Widget\Input\Input;
use hipanel\tests\_support\Page\Widget\Input\Select2;
use hipanel\tests\_support\Step\Acceptance\Seller;

class TariffCest
{
    /**
     * @var IndexPage
     */
    private $index;

    public function _before(Seller $I)
    {
        $this->index = new IndexPage($I);
    }

    public function ensureIndexPageWorks(Seller $I)
    {
        $I->login();
        $I->needPage(Url::to('@tariff'));
        $I->see('Tariffs', 'h1');
        $I->see('Create', 'a');
        $this->ensureICanSeeAdvancedSearchBox();
        $this->ensureICanSeeBulkBillSearchBox();
    }

    private function ensureICanSeeAdvancedSearchBox()
    {
        $this->index->containsFilters([
            new Input('Tariff'),
            new Input('Note'),
            new Input('Type'),
            new Select2('Client'),
            new Select2('Reseller'),
        ]);
    }

    private function ensureICanSeeBulkBillSearchBox()
    {
        $this->index->containsBulkButtons([
            'Copy',
            'Delete',
        ]);
        $this->index->containsColumns([
            'Tariff',
            'Used',
            'Type',
            'Client',
            'Reseller',
        ]);
    }
}
