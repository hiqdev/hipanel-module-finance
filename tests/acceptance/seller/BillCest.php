<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

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
        $this->ensureICanSeeAdvancedSearchBox($I);
        $this->ensureICanSeeBulkBillSearchBox();
    }

    private function ensureICanSeeAdvancedSearchBox(Seller $I)
    {
        $this->index->containsFilters([
            Select2::asAdvancedSearch($I, 'Client'),
            Select2::asAdvancedSearch($I, 'Currency'),
            Select2::asAdvancedSearch($I, 'Type'),
            Input::asAdvancedSearch($I, 'Servers'),
            Input::asAdvancedSearch($I,'Description'),
            Select2::asAdvancedSearch($I, 'Tariff'),
            Select2::asAdvancedSearch($I, 'Reseller'),
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
