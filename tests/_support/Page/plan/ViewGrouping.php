<?php

namespace hipanel\modules\finance\tests\_support\Page\plan;

class ViewGrouping extends View
{
    public function seePlan()
    {
        $I = $this->tester;

        $I->see($this->name);
        $I->see($this->client);
        $I->see($this->type);
        $I->see($this->note);
        $I->see("Grouping", "//span[contains(text(), 'Grouping')]");
    }
}
