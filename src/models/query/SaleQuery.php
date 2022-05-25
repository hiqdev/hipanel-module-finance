<?php
declare(strict_types=1);

namespace hipanel\modules\finance\models\query;

use hiqdev\hiart\ActiveQuery;

class SaleQuery extends ActiveQuery
{
    public function withServer(): self
    {
        return $this->with('server');
    }
}
