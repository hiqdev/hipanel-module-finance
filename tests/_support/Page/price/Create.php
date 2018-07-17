<?php

namespace hipanel\modules\finance\tests\_support\Page\price;

class Create extends View
{
    protected function loadForm(): void
    {
        $I = $this->tester;

        $I->click('Create');
        $I->waitForElement('#create-prices');
    }

    protected function savePrice(): void
    {
        $I = $this->tester;

        $I->click('Save');
        $I->closeNotification('Prices were successfully created');
    }

    public function addPriceForTemplate(int $id, string $priceType): void
    {
        $this->loadPage($id);
        $this->loadForm();
        $this->choosePriceType($priceType);
        $this->proceedToCreation();
        $this->fillRandomPrices('templateprice');
        $this->savePrice();
        $this->seeRandomPrices();
    }

    private function choosePriceType(string $priceType): void
    {
        $I = $this->tester;

        $I->click('//div[contains(@class, "field-type")]/span');
        $I->fillField('.select2-search__field', $priceType);
        $I->waitForElementNotVisible('.loading-results', 120);
        $I->click("//li[contains(text(), '{$priceType}')]");
    }

    public function addPriceForNonTemplate(int $id, string $object, string $template, string $priceType): void
    {
        $this->loadPage($id);
        $this->loadForm();
        $this->findObject($object);
        $this->findTemplate($template);
        $this->choosePriceType($priceType);
        $this->proceedToCreation();
        $this->fillRandomPrices('price');
        $this->savePrice();
        $this->seeRandomPrices();
    }

    private function findObject(string $object): void
    {
        $I = $this->tester;

        $I->click('//div[contains(@class, "field-object_id")]/span');
        $I->fillField('.select2-search__field', $object);
        $I->waitForElementNotVisible('.loading-results', 120);
        $I->click("//li[text()='{$object}']");
    }

    protected function findTemplate(string $template): void
    {
        $I = $this->tester;

        $I->fillField('.select2-search__field', $template);
        $I->waitForElementNotVisible('.loading-results', 120);
        $I->click("//li[contains(text(), '{$template}')]");
    }

    protected function proceedToCreation(): void
    {
        $I = $this->tester;

        $I->click('Proceed to creation');
        $I->wait(1);
    }
}
