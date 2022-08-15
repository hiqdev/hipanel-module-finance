<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\tests\acceptance\seller;

use hipanel\helpers\Url;
use hipanel\tests\_support\Step\Acceptance\Seller;

class AccountRechargingCest
{
    public function ensureIndexPageWorks(Seller $I)
    {
        $I->markTestSkipped('Moved to PW');
        $I->login();
        $I->needPage(Url::to('@pay/deposit'));
        $I->see('Account recharging', 'h1');
        $this->ensureICanSeeDepositBox($I);
        $this->ensureICanSeeWarningBox($I);
    }

    private function ensureICanSeeDepositBox(Seller $I)
    {
        $url = Url::to('@pay/deposit');
        $form = "//form[@action='$url']";
        $I->see('Amount', "$form/label");
        $I->seeElement('input', ['id' => 'depositform-amount']);
        $text = 'Enter the amount of the replenishment in USD. For example: 8.79';
        $I->see($text, $form);
        $I->see('Proceed', "$form/button[@type='submit']");
    }

    private function ensureICanSeeWarningBox(Seller $I)
    {
        $I->see('Important information', 'h4');
        $text = 'Remember to return to the site after successful payment!';
        $I->see($text, 'p');
    }
}
