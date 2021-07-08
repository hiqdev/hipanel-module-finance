<?php

namespace hipanel\modules\finance\tests\acceptance\manager;

use hipanel\helpers\Url;
use Codeception\Example;
use hipanel\modules\finance\tests\_support\Page\bill\Index;
use hipanel\tests\_support\Step\Acceptance\Manager;
use hipanel\tests\_support\Helper\PressButtonHelper;
use hipanel\tests\_support\Page\Widget\Input\MultipleSelect2;
use hipanel\modules\finance\tests\_support\Page\bill\Create;

class PaymentsFilterCest
{
    private Create $create;
    private Index $index;

    public function _before(Manager $I): void
    {
        $this->create = new Create($I);
        $this->index = new Index($I);
    }

    /**
     * @dataProvider provideDataBills
     */
    public function createBillsWithDifferentTypes(Manager $I, Example $example): void
    {
        $dataBill = iterator_to_array($example->getIterator());

        foreach ($dataBill as $bill) {
            $I->needPage(Url::to('@bill/create'));

            $this->create->fillMainBillFields($bill);
            $I->pressButton('Save');
            $this->create->seeActionSuccess();
        }
    }

    /**
     * @dataProvider provideDataBills
     */
    public function ensureTypeFilterWorksCorrectly(Manager $I, Example $example): void
    {
        $dataBill = iterator_to_array($example->getIterator());
        $I->needPage(Url::to('@bill/index'));

        $this->index->setAdvancedFilter(MultipleSelect2::asAdvancedSearch($I, 'Type'), 'Domain Services');
        $I->pressButton('Search');
        $I->waitForPageUpdate();

        foreach ($dataBill as $bill) {
            $this->index->checkBillDataInBulkTable($bill);
        }
    }

    protected function provideDataBills(): array
    {
        return [
            'bills' => [
                'bill1' => [
                    'login'     => 'hipanel_test_user',
                    'type'      => 'Registration',
                    'currency'  => '$',
                    'sum'       =>  250,
                    'quantity'  =>  1,
                ],
                'bill2' => [
                    'login'     => 'hipanel_test_user',
                    'type'      => 'Delete by AGP',
                    'currency'  => '$',
                    'sum'       =>  450,
                    'quantity'  =>  1,
                ],
            ],
        ];
    }
}
