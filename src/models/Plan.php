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
 * @property Price[]|CertificatePrice[] $prices
 * @property-read string[] typeOptions
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class Plan extends \hipanel\base\Model
{
    const TYPE_SERVER = 'server';
    const TYPE_PCDN = 'pcdn';
    const TYPE_VCDN = 'vcdn';
    const TYPE_TEMPLATE = 'template';
    const TYPE_CERTIFICATE = 'certificate';

    use \hipanel\base\ModelTrait;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['id', 'type_id', 'state_id', 'client_id', 'currency_id'], 'integer'],
            [['type', 'state', 'client', 'name', 'note', 'currency'], 'string'],

            [['type', 'name', 'currency'], 'required', 'on' => ['create', 'update']],
            [['id'], 'required', 'on' => ['update', 'delete', 'set-note']],
            [['id'], 'required', 'on' => ['delete', 'restore']],
            [['id', 'server_ids'], 'safe', 'on' => ['copy']],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'name' => Yii::t('hipanel:finance', 'Name'),
            'server_ids' => Yii::t('hipanel.finance.plan', 'Servers'),
        ]);
    }

    public function getPrices()
    {
        if ($this->type === Plan::TYPE_CERTIFICATE) {
            return $this->hasMany(CertificatePrice::class, ['plan_id' => 'id'])->inverseOf('plan');
        }
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

    public function isDeleted(): bool
    {
        return $this->state === 'deleted';
    }
}
