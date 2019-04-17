<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\tests\_support\Page\price\domain;

use hipanel\modules\finance\tests\_support\Page\price\certificate\Create as CreateCertificate;

class Create extends CreateCertificate
{
    protected function seeNoSuggestions(): void
    {
        $I = $this->tester;

        $I->see('No price suggestions for this object');
        $I->see('We could not suggest any new prices of type "Domain" for the selected object.');
        $I->see('Probably, they were already created earlier or this suggestion type is not compatible with this object type');
        $I->see('You can return back to plan');
    }
}
