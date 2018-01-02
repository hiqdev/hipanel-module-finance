<?php

namespace hipanel\modules\finance\grid;

class PlanGridView extends \hipanel\grid\BoxedGridView
{
    public function columns()
    {
        return array_merge(parent::columns(), []);
    }
}
