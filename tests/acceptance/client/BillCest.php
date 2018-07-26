<?php

namespace hipanel\modules\finance\tests\acceptance\client;

use hipanel\helpers\Url;
use hipanel\tests\_support\Page\IndexPage;
use hipanel\tests\_support\Step\Acceptance\Client;

class BillCest
{
    public function ensureIndexPageWorks(Client $I)
    {
        $I->login();
        $I->needPage(Url::to('@bill/index'));
        $I->see('Bills', 'h1');
        $this->ensureICanSeeAdvancedSearchBox($I);
        $this->ensureICanSeeBulkBillSearchBox($I);
    }

    private function ensureICanSeeAdvancedSearchBox(Client $I)
    {
        $I->see('Advanced search');

        $index = new IndexPage($I);
        $index->containsFilters('form-advancedsearch-bill-search', [
            ['input' => ['placeholder' => 'Currency']],
            ['input' => [
                'id' => 'billsearch-time_from',
                'name' => 'date-picker',
            ]],
            ['input' => ['placeholder' => 'Type']],
            ['input' => ['placeholder' => 'Servers']],
            ['input' => ['placeholder' => 'Description']],
        ]);

        $I->see('Date', 'label');
        $I->see('Tariff', 'span');

        $index->containsButtons([
            ['a' => 'Recharge account'],
            ["//button[@type='submit']" => 'Search'],
            ['a' => 'Clear'],
        ]);
    }

    private function ensureICanSeeBulkBillSearchBox(Client $I)
    {
        $index = new IndexPage($I);
        $index->containsColumns('bulk-bill-search', [
            'Time',
            'Sum',
            'Balance',
            'Type',
            'Description',
        ]);
    }
}
