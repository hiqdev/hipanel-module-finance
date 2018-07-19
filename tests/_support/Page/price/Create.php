<?php

namespace hipanel\modules\finance\tests\_support\Page\price;

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
        $this->saveForm();
        $this->seeRandomPrices();
    }

    public function openModal(): void
    {
        $I = $this->tester;

        $I->click('Create');
        $I->waitForElement('#create-prices');
    }

    public function choosePriceType(string $priceType): void
    {
        $I = $this->tester;

        $I->click('//div[contains(@class, "field-type")]/span');
        $I->fillField('.select2-search__field', $priceType);
        $I->waitForElementNotVisible('.loading-results', 120);
        $I->click("//li[contains(text(), '{$priceType}')]");
    }

    public function chooseObject(string $objectName): void
    {
        $I = $this->tester;

        $I->click('//div[contains(@class, "field-object_id")]/span');
        $I->fillField('.select2-search__field', $objectName);
        $I->waitForElementNotVisible('.loading-results', 120);
        $I->click("//li[text()='{$objectName}']");
    }

    public function chooseTemplate(string $templateName): void
    {
        $I = $this->tester;

        $I->fillField('.select2-search__field', $templateName);
        $I->waitForElementNotVisible('.loading-results', 120);
        $js = <<<JS
        $("li:contains('{$templateName}')").each(function() {
            if (this.firstChild.data === '{$templateName}') {
                $(this).trigger('mouseup');
            }
        });
JS;
        $I->executeJS($js);
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
