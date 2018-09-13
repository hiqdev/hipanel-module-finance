<?php

namespace hipanel\modules\finance\tests\acceptance\seller;

use hipanel\helpers\Url;
use hipanel\tests\_support\Page\IndexPage;
use hipanel\tests\_support\Page\Widget\Input\Dropdown;
use hipanel\tests\_support\Page\Widget\Input\Input;
use hipanel\tests\_support\Page\Widget\Input\Select2;
use hipanel\tests\_support\Step\Acceptance\Seller;

class SalesCest
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
        $I->needPage(Url::to('@sale'));
        $I->see('Sales', 'h1');
        $this->ensureICanSeeAdvancedSearchBox($I);
        $this->ensureICanSeeBulkBillSearchBox();
    }

    private function ensureICanSeeAdvancedSearchBox(Seller $I)
    {
        $this->index->containsFilters([
            Select2::asAdvancedSearch($I, 'Seller'),
            Select2::asAdvancedSearch($I, 'Buyer'),
            (Dropdown::asAdvancedSearch($I,'Object type'))->withItems([
                'Servers',
                'IP',
                'Accounts',
                'Clients',
                'Parts',
            ]),
            Select2::asAdvancedSearch($I, 'Tariff'),
            Input::asAdvancedSearch($I, 'Object'),
        ]);
    }

    private function ensureICanSeeBulkBillSearchBox()
    {
        $this->index->containsBulkButtons([
            'Delete',
        ]);
        $this->index->containsColumns([
            'Object',
            'Seller',
            'Buyer',
            'Tariff',
            'Time',
        ]);
    }
}
