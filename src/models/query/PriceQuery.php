<?php

namespace hipanel\modules\finance\models\query;

use hipanel\modules\finance\models\Plan;
use hiqdev\hiart\ActiveQuery;

class PriceQuery extends \hiqdev\hiart\ActiveQuery
{
    public function withFormulaLines(): self
    {
        return $this->addSelect('formula_lines');
    }

    public function withMainObject(): self
    {
        return $this->addSelect('main_object_id')
            ->joinWith('object')
            ->limit(-1);
    }

    public function withPlan(): self
    {
        return $this->joinWith('plan');
    }
}
