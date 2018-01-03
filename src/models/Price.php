<?php

namespace hipanel\modules\finance\models;

class Price extends \hipanel\base\Model
{
    use \hipanel\base\ModelTrait;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['id', 'type_id', 'parent_id', 'plan_id', 'object_id', 'type_id', 'unit_id', 'currency_id'], 'integer'],
            [['type', 'plan', 'unit', 'currency', 'note', 'data'], 'string'],
            [['quantity', 'price'], 'number'],

            [['plan_id', 'type', 'price'], 'required', 'on' => 'create'],
            [['id'], 'required', 'on' => ['update', 'set-note']],
        ]);
    }
}
