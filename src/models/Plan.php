<?php

namespace hipanel\modules\finance\models;

use hipanel\models\Ref;
use Yii;

/**
 * Class Plan
 *
 * @property string $id
 * @property string $name
 * @property string $type
 * @property string $currency
 * @property int $currency_id
 *
 * @property Sale[] $sales
 * @property Price[] $prices
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class Plan extends \hipanel\base\Model
{
    const TYPE_SERVER = 'server';
    const TYPE_TEMPLATE = 'template';

    use \hipanel\base\ModelTrait;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['id', 'type_id', 'state_id', 'client_id', 'currency_id'], 'integer'],
            [['type', 'state', 'client', 'name', 'note', 'currency'], 'string'],

            [['type', 'name', 'currency'], 'required', 'on' => ['create', 'update']],
            [['id'], 'required', 'on' => ['update', 'delete', 'set-note']],
            [['id'], 'required', 'on' => ['delete', 'restore']],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'name' => Yii::t('hipanel:finance', 'Name'),
        ]);
    }

    public function getPrices()
    {
        return $this->hasMany(Price::class, ['plan_id' => 'id'])->indexBy('id')->inverseOf('plan');
    }

    public function getSales()
    {
        return $this->hasMany(Sale::class, ['tariff_id' => 'id']);
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
