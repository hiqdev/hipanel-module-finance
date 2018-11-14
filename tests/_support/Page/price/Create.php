<?php

namespace hipanel\modules\finance\tests\_support\Page\price;

use hipanel\tests\_support\Page\Widget\Input\Select2;

class Create extends View
{
    public function createRandomPrices(string $objectName, string $templateName, string $priceType): void
    {
        $this->loadPage();
        $this->openModal();
        $this->chooseObject($objectName);
        $this->chooseTemplate($templateName);
        $this->choosePriceType($priceType);
        $this->proceedToCreation();
        $this->fillRandomPrices('price');
        // get all xeditable link
        // //a[contains(@class, 'editable')]
        // counts links? or count row?
        // get link, press open input
        // //div[contains(@class,'editable-input')]//input[contains(@type,'text')]
        // write random note and submit
        // //div[contains(@class,'editable-buttons')]//button[contains(@type,'submit')]
        // go to line 'get link' or exit
        // JS?????
        // $$('a[class*=editable]').each(function(){ this.click(); $(".editable-input input").val("1"); $(".editable-submit").click(); });
        $this->saveForm();
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
}
