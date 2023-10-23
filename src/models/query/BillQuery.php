<?php
declare(strict_types=1);

namespace hipanel\modules\finance\models\query;

use hipanel\modules\finance\behaviors\TimeTillAttributeChanger;
use hiqdev\hiart\ActiveQuery;

class BillQuery extends ActiveQuery
{
    public function behaviors(): array
    {
        return [
            ['class' => TimeTillAttributeChanger::class],
        ];
    }
}
