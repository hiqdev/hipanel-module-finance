<?php

namespace hipanel\modules\finance\tests\_support\Page\price\domain;

use hipanel\modules\finance\tests\_support\Page\price\certificate\Create as CreateCertificate;

class Create extends CreateCertificate
{
    protected function seeNoSuggestions(): void
    {
        $I = $this->tester;

        $I->see("No price suggestions for this object");
        $I->see('We could not suggest any new prices of type "Domain" for the selected object.');
        $I->see('Probably, they were already created earlier or this suggestion type is not compatible with this object type');
        $I->see("You can return back to plan");
    }
}
