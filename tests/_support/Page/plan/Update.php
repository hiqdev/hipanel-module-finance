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

class Update extends Create
{
    protected function loadPage(): void
    {
        $I = $this->tester;

        $I->needPage(Url::to(['@plan/update', 'id' => $this->id]));
    }

    protected function savePlan(): void
    {
        $I = $this->tester;

        $I->click('Save');
        $I->closeNotification('Plan was successfully updated');
    }

    public function updatePlan($id)
    {
        $this->id = $id;

        return $this->createPlan();
    }
}
