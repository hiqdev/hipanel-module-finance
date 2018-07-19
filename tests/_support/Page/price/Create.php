<?php

namespace hipanel\modules\finance\tests\_support\Page\price;

use hipanel\tests\_support\Page\Widget\Select2;

class Create extends View
{
    /**
     * @var Select2
     */
    private $select2;

    public function createRandomPrices(string $objectName, string $templateName, string $priceType): void
    {
        $this->select2 = new Select2($this->tester);

        $this->loadPage();
        $this->openModal();
        $this->chooseObject($objectName);
        $this->chooseTemplate($templateName);
        $this->choosePriceType($priceType);
        $this->proceedToCreation();
        $this->fillRandomPrices('price');
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
        $this->select2->open('#object_id');
        $this->select2->fillSearchField($objectName);
        $this->select2->chooseOption($objectName);
    }

    public function chooseTemplate(string $templateName): void
    {
        $this->select2->fillSearchField($templateName);
        $this->select2->chooseOption($templateName);
    }

    public function choosePriceType(string $priceType): void
    {
        $this->select2->open('#type');
        $this->select2->fillSearchField($priceType);
        $this->select2->chooseOption($priceType);
    }

    public function proceedToCreation(): void
    {
        $I = $this->tester;

        $I->click('Proceed to creation');
        $I->waitForText('Create suggested prices');
    }

    public function saveForm(): void
    {
        $I = $this->tester;

        $I->click('Save');
        $I->closeNotification('Prices were successfully created');
    }

}
