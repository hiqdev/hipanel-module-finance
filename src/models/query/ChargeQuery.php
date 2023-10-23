<?php
declare(strict_types=1);

namespace hipanel\modules\finance\models\query;

use hipanel\modules\finance\behaviors\TimeTillAttributeChanger;
use hiqdev\hiart\ActiveQuery;

/**
 * Class ChargeQuery
 * @package hipanel\modules\finance\models\query
 */
class ChargeQuery extends ActiveQuery
{
    public function behaviors(): array
    {
        return [
            ['class' => TimeTillAttributeChanger::class],
        ];
    }

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

    public function withRootChargeType(): self
    {
        $this->andWhere(['with_rootChargeType' => true]);

        return $this;
    }
}
