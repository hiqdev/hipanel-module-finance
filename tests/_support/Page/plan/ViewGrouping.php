<?php

namespace hipanel\modules\finance\tests\_support\Page\plan;

class ViewGrouping extends View
{
    public function seePlan()
    {
        parent::seePlan();
        $I->see("Grouping", "//span[contains(text(), 'Grouping')]");
    }
}
