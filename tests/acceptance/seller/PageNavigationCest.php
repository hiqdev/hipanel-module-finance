<?php

namespace hipanel\modules\finance\tests\acceptance\seller;

use hipanel\helpers\Url;
use hipanel\tests\_support\Step\Acceptance\Seller;

class PageNavigationCest
{
    public function ensurePageNavigationIsCorrect(Seller $I)
    {
        $I->login();
        $url = '@bill/index?per_page=25';
        $I->needPage(Url::to($url));
        $I->waitForElement("//b[2]", 10);
        $totalElements = $I->grabTextFrom("//b[2]");
        $totalPages = intdiv($totalElements , 25) + 1;
        $I->needPage(Url::to($url . "&page=" . $totalPages));
        $I->waitForElement("div[class*='visible-xs-inline']", 5);
        $I->needPage(Url::to($url . "$totalElements&page=" . $totalPages));
        $I->waitForElement("div[class*='visible-xs-inline']", 5);
    }
}
