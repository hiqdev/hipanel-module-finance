<?php


namespace hipanel\modules\finance\models\query;


use hiqdev\hiart\ActiveQuery;

/**
 * Class ChargeQuery
 * @package hipanel\modules\finance\models\query
 */
class ChargeQuery extends ActiveQuery
{
    /**
     * @return $this
     */
    public function withCommonObject(): self
    {
        $this->joinWith('commonObject');
        $this->andWhere(['with_commonObject' => true]);

        return $this;
    }

    /**
     * @return $this
     */
    public function withLatestCommonObject(): self
    {
        $this->joinWith('latestCommonObject');
        $this->andWhere(['with_latestCommonObject' => true]);

        return $this;
    }

    public function withBill(): self
    {
        $this->joinWith('bill');
        $this->andWhere(['with_bill' => true]);

        return $this;
    }
}
