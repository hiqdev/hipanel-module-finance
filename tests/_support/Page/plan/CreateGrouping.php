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

class CreateGrouping extends Create
{
    protected function setGrouping(): void
    {
        $I = $this->tester;

        $I->checkOption("//input[@name='Plan[is_grouping]'][@type='checkbox']");
    }
}
