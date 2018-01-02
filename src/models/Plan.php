<?php

namespace hipanel\modules\finance\models;

class Plan extends \hipanel\base\Model
{
    use \hipanel\base\ModelTrait;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['id', 'type_id', 'state_id', 'client_id'], 'integer'],
            [['type', 'state', 'client', 'name', 'note'], 'string'],

            [['parent_id'], 'required', 'on' => 'create'],
            [['id'], 'required', 'on' => ['update', 'delete', 'set-note']],
        ]);
    }
}
