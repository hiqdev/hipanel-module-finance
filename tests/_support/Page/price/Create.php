<?php

namespace hipanel\modules\finance\tests\_support\Page\price;

use hipanel\tests\_support\Page\IndexPage;
use hipanel\tests\_support\Page\Widget\Input\Select2;

class Create extends View
{
    /**
     * @param string $objectName
     * @param string $templateName
     * @param string $priceType
     */
    public function createRandomPrices(string $objectName, string $templateName, string $priceType): void
    {
        $note = 'test note';

        $this->loadPage();
        $this->openModal();
        $this->chooseObject($objectName);
        $this->chooseTemplate($templateName);
        $this->choosePriceType($priceType);
        $this->proceedToCreation();
        $this->fillRandomPrices('price');
        $this->fillXEditable($note);
        $this->saveForm();
        $this->seeNoteInTbodyRow($note);
        $this->seeRandomPrices();
    }

    public function openModal(): void
    {
        $I = $this->tester;

        $I->click('Create');
        $I->waitForElement('#create-prices');
    }

    public function chooseObject(string $objectName): void
    {
        (new Select2($this->tester, '#object_id'))
            ->setValue($objectName);
    }

    public function chooseTemplate(string $templateName): void
    {
        (new Select2($this->tester, '#template_plan_id'))
            ->fillSearchField($templateName)
            ->chooseOptionLike($templateName);
    }

    public function choosePriceType(string $priceType): void
    {
        (new Select2($this->tester, '#type'))
            ->setValue($priceType);
    }

    public function proceedToCreation(): void
    {
        $I = $this->tester;

        $I->pressButton('Proceed to creation');
        $I->waitForText('Create suggested prices', 60);
    }

    public function saveForm(): void
    {
        $I = $this->tester;

        $I->click('Save');
        $I->closeNotification('Prices were successfully created');
    }

    /**
     * @param string $text
     */
    public function fillXEditable(string $text): void
    {
        $this->tester->executeJS("
            $('a[class*=editable]').each(function(){
            this.click();
            $('.editable-input input').val('{$text}');
            $('.editable-submit').click();
            });
        ");
    }

    /**
     * @param string $note
     */
    public function seeNoteInTbodyRow(string $note): void
    {
        $page = new IndexPage($this->tester);
        $howRow = $page->countRowsInTableBody();
        foreach (range(1, $howRow) as $i) {
            $this->tester->see($note, "//tbody/tr[$i]");
        }
    }
}

