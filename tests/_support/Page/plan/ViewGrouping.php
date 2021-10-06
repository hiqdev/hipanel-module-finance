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

class ViewGrouping extends View
{
    public function seePlan()
    {
        parent::seePlan();
        $this->tester->see('Grouping', '//span');
    }
}
