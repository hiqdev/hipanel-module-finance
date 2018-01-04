<?php

namespace hipanel\modules\finance\models;

use hipanel\models\Ref;
use Yii;

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

    public function attributeLabels()
    {
        return [
            'plan_ilike' => Yii::t('hipanel.finance.plan', 'Plan'),
        ];
    }

    public function getTypeOptions()
    {
        return Ref::getList('type,bill', null, ['pnames' => 'monthly,overuse', 'with_recursive' => 1]);
    }

    public function getUnitOptions()
    {
        return Ref::getList('type,unit', null, ['with_recursive' => 1]);
    }

    public function getCurrencyOptions()
    {
        return Ref::getList('type,currency');
    }
}
