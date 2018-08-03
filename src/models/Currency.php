<?php

namespace hipanel\modules\finance\models;

use hipanel\models\Ref;
use yii\base\Model;

class Currency extends Model
{
    /**
     * @return array list of possible currencies
     */
    public static function list(): array
    {
        return Ref::getList('type,currency', 'hipanel', ['orderby' => 'no_asc']);
    }
}