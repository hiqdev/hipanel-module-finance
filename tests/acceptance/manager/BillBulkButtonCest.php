<?php
declare(strict_types=1);

namespace hipanel\modules\finance\tests\acceptance\manager;

use hipanel\helpers\Url;
use Codeception\Example;
use hipanel\tests\_support\Page\IndexPage;
use hipanel\tests\_support\Step\Acceptance\Manager;
use hipanel\modules\finance\tests\_support\Page\bill\Create;

class BillBulkButtonCest
{
    /**
     * @dataProvider BillDataProvider
     */
    public function ensureBulkButtonsWorkCorrectly(Manager $I, Example $example): void
    {
        $I->login();
        $example = iterator_to_array($example->getIterator());
        $I->needPage(Url::to('@bill/index/?sort=-time'));

        $this->checkRows($I, [1, 2]);
        $I->pressButton($example['action']);

        $this->{$example['method']}($I, $example['descrp']);
    }

    private function checkRows(Manager $I, array $rows): void
    {
        foreach ($rows as $row) {
            $I->checkOption("//tbody//tr[$row]//input");
        }
    }

    private function ensureUpdateAndCopyBulkButtonsWorkCorrectly(Manager $I, array $description): void
    {
        $this->fillDescriptionField($I, $description);
        $this->checkDataInTable($I, $description);
    }

    private function ensureDeleteBulkButtonWorksCorrectly(Manager $I, array $description): void
    {
        $index = new IndexPage($I);

        $I->acceptPopup();
        $I->waitForPageUpdate();

        $I->seeInCurrentUrl('?sort=-time');

        foreach ($description as $field) {
            $I->cantSee($field, '//td[' . $index->getColumnNumber('Description') . ']');
        }
    }

    private function fillDescriptionField(Manager $I, array $description): void
    {
        $create = new Create($I);

        foreach ($description as $key => $field) {
            $create->fillBillDescriptionField($field, $key);
        }

        $I->pressButton('Save');
        $I->waitForPageUpdate();
    }

    private function checkDataInTable(Manager $I, array $description): void
    {
        $index = new IndexPage($I);

        foreach ($description as $field) {
            $I->see($field, '//td[' . $index->getColumnNumber('Description') . ']');
        }
    }

    protected function BillDataProvider(): array
    {
        return [
            [
                'action' => 'Update',
                'method' => 'ensureUpdateAndCopyBulkButtonsWorkCorrectly',
                'descrp' => [
                    'Successful Update Test #1',
                    'Successful Update Test #2',
                ],
            ],
            [
                'action' => 'Copy',
                'method' => 'ensureUpdateAndCopyBulkButtonsWorkCorrectly',
                'descrp' => [
                    'Successful Copy Test #1',
                    'Successful Copy Test #2',
                ],
            ],
            [
                'action' => 'Delete',
                'method' => 'ensureDeleteBulkButtonWorksCorrectly',
                'descrp' => [
                    'Successful Copy Test #1',
                    'Successful Copy Test #2',
                ],
            ],
        ];
    }
}
