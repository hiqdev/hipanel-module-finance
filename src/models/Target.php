<?php

namespace hipanel\modules\finance\models;

use hipanel\base\Model;
use hipanel\base\ModelTrait;

class Target extends Model
{
    use ModelTrait;

    /**
     * {@inheritdoc}
     * @return array
     */
    public function rules()
    {
        return [
            [['id', 'type_id', 'state_id', 'client_id', 'access_id'], 'integer'],
            [['type', 'state', 'client', 'name'], 'string'],
        ];
    }
}
