<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\tests\_support\Page\price;

use hipanel\tests\_support\Page\Widget\Input\Select2;
use hipanel\helpers\Url;
use hipanel\modules\finance\tests\_support\Helper\CurrencyListTrait;
use hipanel\tests\_support\Page\Widget\Input\XEditable;

class Create extends View
{
    use CurrencyListTrait;

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
        $howNotes = $this->fillNote($note);
        $this->saveForm();
        $this->seeNoteInTbodyRow($note, $howNotes);
        $this->seeRandomPrices();
    }

    public function openModal(): void
    {
        $I = $this->tester;

        $I->click("//div/a[contains(text(), 'Create prices')]");
        $I->click("//li/a[contains(text(), 'Create prices')]");
        $I->waitForElement('#create-prices');
    }

    public function createSharedPrice(array $priceData): void
    {
        $I = $this->tester;

        $I->clickLink('Create price');
        $I->clickLink('Create shared price');
        $I->waitForElement('#template_plan_id');

        $this->chooseTemplate($priceData['plan']);
        $this->choosePriceType($priceData['type']);
        $this->proceedToCreation();
    }

    public function createTemplatePrices(array $priceData): void
    {
        $I = $this->tester;
        $this->openModal();

        $this->choosePriceType($priceData['type']);
        $this->proceedToCreation();
    }

    public function chooseObject(string $objectName): void
    {
        (new Select2($this->tester, '#object_id'))
            ->setValue($objectName);
    }

    public function chooseTemplate(string $templateName): void
    {
        $this->tester->click("//span[@id='select2-template_plan_id-container']");
        (new Select2($this->tester, '#template_plan_id'))->setValue($templateName);
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
        $I->waitForPageUpdate();
        $I->closeNotification('Prices were successfully created');
    }

    /**
     * @param string $note
     * @return int
     */
    public function fillNote(string $note): int
    {
        $how = count($this->tester->grabMultiple("//a[contains(@class, 'editable')]"));
        foreach (range(1, $how) as $i) {
            (new XEditable($this->tester, "div.price-item:nth-child($i)"))
                ->setValue("$note $i");
        }

        return $how;
    }

    /**
     * @param string $note
     * @param int $how
     */
    public function seeNoteInTbodyRow(string $note, int $how): void
    {
        foreach (range(1, $how) as $i) {
            $this->tester->see("$note $i", '//tbody');
        }
    }

    public function lookForHelpTip(string $currentCurrency, array $sharedPriceData): void
    {
        $I = $this->tester;

        $this->updatePlanWithNewCurrency($currentCurrency, $this->getId());
        $I->waitForText('Create prices', 10);
        $this->createSharedPrice($sharedPriceData);
        $I->waitForElement("div[class*='0'] button[class*='formula-help']");
        $I->click("div[class*='0'] button[class*='formula-help']");
        $I->waitForText($currentCurrency);
    }

    public function updatePlanWithNewCurrency(string $currency, string $planId): void
    {
        $I = $this->tester;

        $I->needPage(Url::to('@plan/update?id=' . $planId));
        (new Select2($I, '#plan-currency'))
            ->setValueLike($currency);
        $I->click('Save');
    }
}
