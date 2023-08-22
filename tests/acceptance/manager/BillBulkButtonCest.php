<?php
declare(strict_types=1);

namespace hipanel\modules\finance\tests\acceptance\manager;

use hipanel\helpers\Url;
use Codeception\Example;
use hipanel\modules\finance\tests\_support\Page\bill\Index;
use hipanel\tests\_support\Page\IndexPage;
use hipanel\tests\_support\Page\Widget\Input\Select2;
use hipanel\tests\_support\Step\Acceptance\Manager;
use hipanel\modules\finance\tests\_support\Page\bill\Create;

class BillBulkButtonCest
{
    private Index $index;

    public function _before(Manager $I): void
    {
        $this->index = new Index($I);
    }

    /**
     * @dataProvider updateDataProvider
     */
    public function ensureUpdateBulkButtonsWorkCorrectly(Manager $I, Example $example): void
    {
        $I->login();
        $example = iterator_to_array($example->getIterator());

        $this->goToBillPageAndPrepareForAction($I, $example['client']);

        $I->pressButton('Update');

        $this->fillDescriptionField($I, $example['descrp']);
        $this->checkDataInTable($I, $example['descrp']);
    }

    /**
     * @dataProvider copyAndDeleteDataProvider
     */
    public function ensureCopyBulkButtonsWorkCorrectly(Manager $I, Example $example): void
    {
        $I->login();
        $example = iterator_to_array($example->getIterator());

        $this->goToBillPageAndPrepareForAction($I, $example['client']);

        $I->pressButton('Update');

        $this->fillDescriptionField($I, $example['descrp']);
        $this->checkDataInTable($I, $example['descrp']);
    }

    /**
     * @dataProvider copyAndDeleteDataProvider
     */
    public function ensureDeleteBulkButtonsWorkCorrectly(Manager $I, Example $example): void
    {
        $I->login();
        $example = iterator_to_array($example->getIterator());

        $this->goToBillPageAndPrepareForAction($I, $example['client']);

        $I->pressButton('Delete');
        $I->acceptPopup();
    }

    private function goToBillPageAndPrepareForAction(Manager $I, $client): void
    {
        $I->needPage(Url::to('@bill/index/?sort=-time'));

        $this->index->filterBy(Select2::asTableFilter($I, 'Client'), $client);

        $I->checkOption("//tbody//tr[1]//input");
        $I->checkOption("//tbody//tr[2]//input");
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
            $create->fillBillDescriptionField($key, $field);
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

    protected function updateDataProvider(): array
    {
        return [
            [
                'descrp' => [
                    'Successful Update Test #1',
                    'Successful Update Test #2',
                ],
                'client' => 'hipanel_test_user',
            ],
        ];
    }
    protected function copyAndDeleteDataProvider(): array
    {
        return [
            [
                'descrp' => [
                    'Successful Copy Test #1',
                    'Successful Copy Test #2',
                ],
                'client' => 'hipanel_test_user',
            ],
        ];
    }
}
