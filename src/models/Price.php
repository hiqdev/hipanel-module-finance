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

            [['plan_id', 'type', 'price', 'currency'], 'required', 'on' => 'create'],
            [['id'], 'required', 'on' => ['update', 'set-note']],
        ]);
    }

    public function attributeLabels()
    {
        return [
            'plan_ilike' => Yii::t('hipanel:finance', 'Plan'),
            'plan_id' => Yii::t('hipanel:finance', 'Plan'),
            'quantity' => Yii::t('hipanel:finance', 'Quantity'),
            'unit' => Yii::t('hipanel:finance', 'Unit'),
            'price' => Yii::t('hipanel:finance', 'Price'),
            'note' => Yii::t('hipanel', 'Note'),
            'type' => Yii::t('hipanel', 'Type'),
        ];
    }

    public function getTypeOptions()
    {
        return Ref::getList('type,bill', null, [
            'select' => 'oname_label',
            'pnames' => 'monthly,overuse',
            'with_recursive' => 1,
            'mapOptions' => ['from' => 'oname'],
        ]);
    }

    public function getUnitOptions()
    {
        return Ref::getList('type,unit', null, [
            'with_recursive' => 1,
            'select' => 'oname_label',
            'mapOptions' => ['from' => 'oname'],
        ]);
    }

    public function getCurrencyOptions()
    {
        return Ref::getList('type,currency');
    }
}