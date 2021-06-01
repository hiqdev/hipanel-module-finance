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
use hipanel\tests\_support\Page\Widget\Input\Select2;
use hipanel\tests\_support\Page\Widget\Input\Dropdown;

class Create extends Plan
{
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

    public function createPlan($partData): int
    {
        $this->loadPage();
        $this->fillMainFields($partData);
        $this->savePlan();

        return $this->id;
    }

    private function fillMainFields($partData): void
    {
        $I = $this->tester;
        $I->fillField(['name' => 'Plan[name]'], uniqid());

        (new Dropdown($this->tester, "//select[@id='plan-type']"))
            ->setValue($partData['type']);

        (new Select2($this->tester, '#plan-currency'))
            ->setValueLike($partData['currency']);
    }

    public function seeFields()
    {
        $this->loadPage();
        $this->seeLabels();
        $this->seeTypeDropdownList();
        $this->seeCurrencyDropdownList();
    }

    public function createSharedPrice($priceData)
    {
        $I = $this->tester;
        $I->click("//a[contains(text(), 'Create price')]");
        $I->click("//a[contains(text(), 'Create shared price')]");
        $I->waitForElement('#template_plan_id');
        (new Select2($this->tester, '#template_plan_id'))
            ->setValueLike($priceData['plan']);
        (new Select2($this->tester, '#type'))
            ->setValueLike($priceData['type']);
        $I->click("//button[contains(text(), 'Proceed to creation')]");

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
            'server' => 'Server',
            'vcdn' => 'vCDN',
            'pcdn' => 'pCDN',
            'ip' => 'IP',
            'account' => 'Account',
            'domain' => 'Domain',
            'client' => 'Client',
            'template' => 'Template',
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
        $list = [
            'usd' => 'USD',
            'eur' => 'EUR',
            'uah' => 'UAH',
            'rub' => 'RUB',
            'pln' => 'PLN',
            'btc' => 'BTC',
        ];
        foreach ($list as $key => $text) {
            $I->see($text, "//select/option[@value='{$key}']");
        }
        $I->clickWithLeftButton('h1');
    }
}
