<?php

namespace hipanel\modules\finance\tests\_support\Page\plan;

class CreateGrouping extends Create
{
    protected function setGrouping()
    {
        $I = $this->tester;

        $I->checkOption("//input[@name='Plan[is_grouping]'][@type='checkbox']");
    }
}
