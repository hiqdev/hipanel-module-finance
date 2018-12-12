<?php

namespace hipanel\modules\finance\models;

use hipanel\modules\finance\logic\bill\QuantityTrait;
use Yii;

/**
 * Class Charge
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 *
 * @property int $id
 * @property string $unit
 * @property string $quantity
 * @property int $parent_id
 * @property string $type
 * @property string $name
 * @property string $ftype
 * @property string $currency
 * @property float $sum
 * @property int object_id
 * @property TargetObject $commonObject
 * @property TargetObject $latestCommonObject
 */
class Charge extends \hiqdev\hiart\ActiveRecord
{
    use QuantityTrait;

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    private $isNewRecord;

    public function rules()
    {
        return [
            [['id', 'type_id', 'object_id', 'bill_id', 'parent_id'], 'integer'],
            [['class', 'name', 'unit'], 'string'],
            [['type', 'label', 'ftype', 'time', 'type_label', 'currency'], 'safe'],
            [['sum', 'quantity'], 'number'],
            [['unit'], 'default', 'value' => 'items', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['object_id', 'sum', 'type', 'quantity', 'unit'], 'required', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['id'], 'safe', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]]
        ];
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'sum' => Yii::t('hipanel', 'Sum'),
            'type' => Yii::t('hipanel', 'Type'),
            'quantity' => Yii::t('hipanel', 'Quantity'),
            'label' => Yii::t('hipanel', 'Description'),
            'time' => Yii::t('hipanel', 'Time'),
            'object_id' => Yii::t('hipanel', 'Object'),
        ]);
    }

    public function markAsNotNew()
    {
        $this->isNewRecord = false;
    }

    public function getCommonObject()
    {
        return $this->hasOne(TargetObject::class, ['id' => 'id']);
    }

    public function getLatestCommonObject()
    {
        return $this->hasOne(TargetObject::class, ['id' => 'id']);
    }

    public function getIsNewRecord()
    {
        return $this->isNewRecord !== false && parent::getIsNewRecord();
    }

    public function typeLabel(): string
    {
        return Yii::t('hipanel.finance.priceTypes', \yii\helpers\Inflector::titleize($this->type));
    }

    public function isMonthly(): bool
    {
        return strpos($this->ftype, 'monthly,') === 0;
    }
}
