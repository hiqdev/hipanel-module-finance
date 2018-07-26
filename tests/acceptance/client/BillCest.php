<?php

namespace hipanel\modules\finance\tests\acceptance\client;

use hipanel\helpers\Url;
use hipanel\tests\_support\Page\GridView;
use hipanel\tests\_support\Step\Acceptance\Client;

class BillCest
{
    public function ensureIndexPageWorks(Client $I)
    {
        $I->login();
        $I->needPage(Url::to('@bill/index'));
        $I->see('Bills', 'h1');
        $I->seeLink('Recharge account', Url::to('@pay/deposit'));
        $this->ensureICanSeeAdvancedSearchBox($I);
        $this->ensureICanSeeBulkBillSearchBox($I);
    }

    private function ensureICanSeeAdvancedSearchBox(Client $I)
    {
        $I->see('Advanced search');
        $placeholders = ['Currency', 'Type', 'Servers', 'Description'];
        foreach ($placeholders as $placeholder) {
            $I->seeElement('input', ['placeholder' => $placeholder]);
        }
        $I->see('Date', 'label');
        $I->see('Tariff', 'span');
        $I->see('Search', "//button[@type='submit']");
        $I->seeLink('Clear', Url::to('@bill/index'));
    }

    private function ensureICanSeeBulkBillSearchBox(Client $I)
    {
        $sortColumns = [
            'time' => 'Time',
            'sum' => 'Sum',
            'balance' => 'Balance',
            'type_label' => 'Type',
            'descr' => 'Description',
        ];
        $gridView = new GridView($I);
        $gridView->containsColumns($sortColumns, '@bill/index');

        $I->see('No results found.', "//div[@class='empty']");
    }
}
