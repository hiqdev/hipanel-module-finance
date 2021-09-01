<?php
declare(strict_types=1);

namespace hipanel\modules\finance\tests\acceptance\seller;

use hipanel\helpers\Url;
use hipanel\tests\_support\Page\IndexPage;
use hipanel\tests\_support\Page\Widget\Input\Select2;
use hipanel\tests\_support\Page\Widget\Input\MultipleSelect2;
use hipanel\tests\_support\Step\Acceptance\Seller;

class SalesCest
{
    public function ensureIndexPageWorks(Seller $I): void
    {
        $I->login();
        $I->needPage(Url::to('@purse'));
        $I->see('Purses', 'h1');
        $this->ensureICanSeeAdvancedSearchBox($I);
        $this->ensureICanSeeBulkBillSearchBox($I);
    }

    private function ensureICanSeeAdvancedSearchBox(Seller $I): void
    {
        $index = new IndexPage($I);

        $index->containsFilters([
            Select2::asAdvancedSearch($I, 'Reseller'),
            Select2::asAdvancedSearch($I, 'Client'),
            MultipleSelect2::asAdvancedSearch($I, 'Currency'),
        ]);
    }

    private function ensureICanSeeBulkBillSearchBox(Seller $I): void
    {
        $index = new IndexPage($I);

        $index->containsBulkButtons([
            'Update',
        ]);
        $index->containsColumns([
            'Reseller',
            'Client',
            'Payment details',
            'Contact',
            'Balance',
            'Credit',
        ]);
    }
}
