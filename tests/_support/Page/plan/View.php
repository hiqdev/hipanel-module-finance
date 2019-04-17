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
use hipanel\tests\_support\AcceptanceTester;

class View extends Plan
{
    public function __construct(AcceptanceTester $I, $fields, $id)
    {
        parent::__construct($I, $fields);

        $this->id = $id;
    }

    public function visitPlan()
    {
        $I = $this->tester;

        $I->needPage(Url::to(['@plan/view', 'id' => $this->id]));
        $I->see('Prices');

        return $this;
    }

    public function seePlan()
    {
        $I = $this->tester;

        $I->see($this->name);
        $I->see($this->client);
        $I->see($this->type);
        $I->see($this->note);
    }
}
