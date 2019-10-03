<?php


namespace hipanel\modules\finance\models\query;


use hiqdev\hiart\ActiveQuery;

class ChargeQuery extends ActiveQuery
{
    public function withCommonObject(): self
    {
        $this->joinWith('commonObject');
        $this->andWhere(['with_commonObject' => true]);

        return $this;
    }

    public function withLatestCommonObject(): self
    {
        $this->joinWith('latestCommonObject');
        $this->andWhere(['with_latestCommonObject' => true]);

        return $this;
    }
}
