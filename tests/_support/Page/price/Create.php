<?php

namespace hipanel\modules\finance\tests\_support\Page\price;

class Create extends View
{
    private function loadForm()
    {
        $I = $this->tester;

        $I->click('Create');
        $I->waitForElement('#create-prices');
    }

    private function savePrice()
    {
        $I = $this->tester;

        $I->click('Save');
        $I->closeNotification('Prices were successfully created');
    }

    public function addPriceForTemplate($id, $priceType)
    {
        $this->loadPage($id);
        $this->loadForm();
        $this->choosePriceType($priceType);
        $this->proceedToCreation('Create suggested prices');
        $this->fillRandomPrices('templateprice');
        $this->savePrice();
        $this->seeRandomPrices();
    }

    private function choosePriceType($priceType)
    {
        $I = $this->tester;

        $I->click('//div[contains(@class, "field-type")]/span');
        $I->fillField('.select2-search__field', $priceType);
        $I->waitForElementNotVisible('.loading-results', 120);
        $I->click("//li[contains(text(), '{$priceType}')]");
    }

    public function addPriceForNonTemplate($id, $object, $template, $priceType)
    {
        $this->loadPage($id);
        $this->loadForm();
        $this->findObject($object);
        $this->findTemplate($template);
        $this->choosePriceType($priceType);
        $this->proceedToCreation('Create suggested prices');
        $this->fillRandomPrices('price');
        $this->savePrice();
        $this->seeRandomPrices();
    }

    public function addPriceForCertificate($id, $template)
    {
        $I = $this->tester;

        $this->loadPage($id);
        $this->loadForm();
        $I->click('//div[contains(@class, "field-template_plan_id")]/span');
        $this->findTemplate($template);
        $this->proceedToCreation('Create prices');
        $this->fillRandomCertificatePrices();
        $this->savePrice();
        $this->seeRandomPrices();
    }

    private function findObject($object)
    {
        $I = $this->tester;

        $I->click('//div[contains(@class, "field-object_id")]/span');
        $I->fillField('.select2-search__field', $object);
        $I->waitForElementNotVisible('.loading-results', 120);
        $I->click("//li[text()='{$object}']");
    }

    private function findTemplate($template)
    {
        $I = $this->tester;

        $I->fillField('.select2-search__field', $template);
        $I->waitForElementNotVisible('.loading-results', 120);
        $I->click("//li[contains(text(), '{$template}')]");
    }

    private function proceedToCreation($text)
    {
        $I = $this->tester;

        $I->click('Proceed to creation');
        $I->waitForText($text);
    }
}
