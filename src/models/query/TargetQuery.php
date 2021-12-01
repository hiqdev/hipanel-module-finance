<?php
declare(strict_types=1);

namespace hipanel\modules\finance\models\query;


class TargetQuery extends \hiqdev\hiart\ActiveQuery
{
    public function withSales(): self
    {
        $this->joinWith('sales');
        $this->andWhere(['with_sales' => true]);

        return $this;
    }
}
