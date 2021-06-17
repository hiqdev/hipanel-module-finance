<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\tests\_support\Page\plan;

use hipanel\helpers\Url;
use hipanel\modules\finance\tests\_support\Helper\CurrencyListTrait;
use hipanel\tests\_support\Page\Widget\Input\Select2;

class Create extends Plan
{
    use CurrencyListTrait;

    protected function loadPage()
    {
        $I = $this->tester;

        $I->needPage(Url::to('@plan/create'));
    }

    protected function savePlan()
    {
        $I = $this->tester;

        $I->click('Save');
        $I->closeNotification('Plan was successfully created');

        $this->id = $I->grabFromCurrentUrl('/id=(\d+)/');
    }

    public function createPlan(): int
    {
        $this->loadPage();
        $this->fillName();
        $this->chooseType();
        $this->setGrouping();
        $this->findClient();
        $this->chooseCurrency();
        $this->fillNote();
        $this->savePlan();

        return $this->id;
    }

    private function fillName()
    {
        $I = $this->tester;

        $I->fillField(['name' => 'Plan[name]'], $this->name);
    }

    private function chooseType()
    {
        $I = $this->tester;

        $I->click(['name' => 'Plan[type]']);
        $I->click("//select/option[.='{$this->type}']");
    }

    protected function setGrouping()
    {
        $I = $this->tester;

        $I->uncheckOption("//input[@name='Plan[is_grouping]'][@type='checkbox']");
    }

    private function findClient()
    {
        (new Select2($this->tester, '#plan-client'))
            ->setValue($this->client);
    }

    private function chooseCurrency()
    {
        (new Select2($this->tester, '#plan-currency'))
            ->setValueLike($this->currency);
    }

    private function fillNote()
    {
        $I = $this->tester;

        $I->fillField(['name' => 'Plan[note]'], $this->note);
    }

    public function seeFields()
    {
        $this->loadPage();
        $this->seeLabels();
        $this->seeTypeDropdownList();
        $this->seeCurrencyDropdownList();
    }

    private function seeLabels()
    {
        $I = $this->tester;

        $list = ['Name', 'Type', 'Seller', 'Currency', 'Note'];
        foreach ($list as $label) {
            $I->see($label, "//label[@class='control-label']");
        }
    }

    private function seeTypeDropdownList()
    {
        $I = $this->tester;

        $list = [
            'server' => 'Server tariff',
            'domain' => 'Domain tariff',
            'switch' => 'Switch tariff',
            'template' => 'Template tariff',
        ];
        $I->click(['name' => 'Plan[type]']);
        foreach ($list as $key => $text) {
            $I->see($text, "//select/option[@value='{$key}']");
        }
        $I->clickWithLeftButton('h1');
    }

    private function seeCurrencyDropdownList()
    {
        $I = $this->tester;

        $I->click("//select[@name='Plan[currency]']/../span");
        foreach ($this->getCurrencyList() as $key => $text) {
            $I->see($text, "//select/option[@value='{$key}']");
        }
        $I->clickWithLeftButton('h1');
    }
}
