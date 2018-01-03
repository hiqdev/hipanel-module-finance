<?php

namespace hipanel\modules\finance\models;

use hipanel\models\Ref;

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

    public function getPrices()
    {
        return $this->hasMany(Price::class, ['plan_id' => 'id']);
    }

    public function getTypeOptions()
    {
        return Ref::getList('type,tariff');
    }

    public function getStateOptions()
    {
        return Ref::getList('state,tariff');
    }
}
