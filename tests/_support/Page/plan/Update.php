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
use hipanel\tests\_support\Page\Widget\Input\Select2;

class Update extends Create
{
    protected function loadPage()
    {
        $I = $this->tester;

        $I->needPage(Url::to(['@plan/update', 'id' => $this->id]));
    }

    protected function savePlan()
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

    public function updatePlanWithNewCurrency($currency, $id): void
    {
        $I = $this->tester;
        $I->needPage(Url::to('@plan/update?id='. $id));
        (new Select2($I, '#plan-currency'))
            ->setValueLike($currency);
        $I->click('Save');
    }
}
