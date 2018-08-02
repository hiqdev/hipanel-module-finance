<?php

namespace hipanel\modules\finance\tests\acceptance\seller;

use hipanel\helpers\Url;
use hipanel\tests\_support\Page\IndexPage;
use hipanel\tests\_support\Page\Widget\Input\Input;
use hipanel\tests\_support\Page\Widget\Input\Select2;
use hipanel\tests\_support\Step\Acceptance\Seller;

class BillCest
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
        $I->needPage(Url::to('@bill'));
        $I->see('Bills', 'h1');
        $I->seeLink('Recharge account', Url::to('@pay/deposit'));
        $I->seeLink('Add payment', Url::to('@bill/create'));
        $I->seeLink('Currency exchange', Url::to('@bill/create-exchange'));
        $I->seeLink('Import payments', Url::to('@bill/import'));
        $this->ensureICanSeeAdvancedSearchBox();
        $this->ensureICanSeeBulkBillSearchBox();
    }

    private function ensureICanSeeAdvancedSearchBox()
    {
        $this->index->containsFilters([
            new Select2('Client'),
            new Input('Currency'),
            new Input('Type'),
            new Input('Servers'),
            new Input('Description'),
            new Select2('Tariff'),
            new Select2('Reseller'),
        ]);
    }

    private function ensureICanSeeBulkBillSearchBox()
    {
        $this->index->containsBulkButtons([
            'Copy',
            'Update',
            'Delete',
        ]);
        $this->index->containsColumns([
            'Client',
            'Time',
            'Sum',
            'Balance',
            'Type',
            'Description',
        ]);
    }
}
