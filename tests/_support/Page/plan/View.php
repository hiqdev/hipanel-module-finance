<?php

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

    public function seeNewPlan()
    {
        $I = $this->tester;

        $I->see($this->name);
        $I->see($this->client);
        $I->see($this->type['value']);
        $I->see($this->note);
    }
}
