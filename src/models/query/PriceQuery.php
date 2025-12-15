<?php declare(strict_types=1);

namespace hipanel\modules\finance\models\query;

use hiqdev\hiart\ActiveQuery;

class PriceQuery extends ActiveQuery
{
    public function withFormulaLines(): self
    {
        return $this->addSelect('formula_lines');
    }

    public function withMainObject(): self
    {
        return $this->addSelect(['main_object_id', 'object'])
                    ->joinWith('object')
                    ->limit(-1);
    }

    public function withPlan(): self
    {
        return $this->addSelect('plan')->joinWith('plan');
    }
}
