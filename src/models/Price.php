<?php

namespace hipanel\modules\finance\models;

use hipanel\models\Ref;
use hipanel\modules\finance\models\factories\PriceModelFactory;
use Yii;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/**
 * Class Price
 *
 * @property int $id
 * @property int $plan_id
 * @property string|int $object_id
 * @property string|float $price
 * @property string $currency
 * @property string|int $main_object_id
 * @property string $unit
 *
 * @property TargetObject $object
 * @property Plan $plan
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class Price extends \hipanel\base\Model
{
    use \hipanel\base\ModelTrait;

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_DELETE = 'delete';

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['id', 'parent_id', 'plan_id', 'object_id', 'type_id', 'unit_id', 'currency_id', 'main_object_id'], 'integer'],
            [['type', 'plan_name', 'unit', 'currency', 'note', 'data'], 'string'],
            [['quantity', 'price'], 'number'],
            [['class'], 'string'], // todo: probably, refactor is needed

            [['plan_id', 'type', 'price', 'currency'], 'required', 'on' => ['create', 'update']],
            [['id'], 'required', 'on' => ['update', 'set-note', 'delete']],
            [['class'], 'default', 'value' => function ($model) {
                return (new \ReflectionClass($this))->getShortName();
            }],
            [['class'], 'string'],
        ]);
    }

    public function attributeLabels()
    {
        return [
            'plan_id' => Yii::t('hipanel:finance', 'Plan'),
            'plan' => Yii::t('hipanel:finance', 'Plan'),
            'quantity' => Yii::t('hipanel:finance', 'Prepaid'),
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

    public function getObject()
    {
        return $this->hasOne(TargetObject::class, ['id' => 'id']);
    }

    public function getPlan()
    {
        return $this->hasOne(Plan::class, ['id' => 'plan_id']);
    }

    public static function tableName()
    {
        return Inflector::camel2id(StringHelper::basename(__CLASS__), '-');
    }

    /**
     * {@inheritdoc}
     */
    public static function instantiate($row)
    {
        if (empty($row['class'])) {
            return parent::instance($row);
        }

        /** @var PriceModelFactory $factory */
        $factory = Yii::$container->get(PriceModelFactory::class);
        return $factory->build($row['class']);
    }
}
