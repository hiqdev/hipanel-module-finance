<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\models;

use hipanel\models\Ref;
use hipanel\modules\finance\models\factories\PriceModelFactory;
use Money\Money;
use Money\MoneyParser;
use Yii;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/**
 * Class Price.
 *
 * @property int $id
 * @property int $plan_id
 * @property string|int $object_id
 * @property string|float $price
 * @property string $currency
 * @property string|int $main_object_id
 * @property string $main_object_name
 * @property string $unit
 * @property string $type
 * @property string $quantity
 * @property string $formula
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
            [['type', 'type_label', 'plan_name', 'unit', 'currency', 'note', 'data', 'main_object_name'], 'string'],
            [['quantity', 'price'], 'number'],
            [['class'], 'string'], // todo: probably, refactor is needed

            [['plan_id', 'type', 'price', 'currency'], 'required', 'on' => ['create', 'update']],
            [['id'], 'required', 'on' => ['update', 'set-note', 'delete']],
            [['class'], 'default', 'value' => function ($model) {
                return (new \ReflectionClass($this))->getShortName();
            }],
            [['class'], 'string'],
            [['formula'], 'string', 'on' => ['create', 'update']], // TODO syn check
        ]);
    }

    public function attributeLabels()
    {
        return [
            'plan_id' => Yii::t('hipanel:finance', 'Tariff plan'),
            'plan' => Yii::t('hipanel:finance', 'Tariff plan'),
            'quantity' => Yii::t('hipanel:finance', 'Prepaid'),
            'unit' => Yii::t('hipanel:finance', 'Unit'),
            'price' => Yii::t('hipanel:finance', 'Price'),
            'formula' => Yii::t('hipanel.finance.price', 'Formula'),
            'note' => Yii::t('hipanel', 'Note'),
            'type' => Yii::t('hipanel', 'Type'),
        ];
    }

    public function getTypeOptions()
    {
        return Ref::getList('type,bill', null, [
            'select' => 'name',
            'pnames' => 'monthly,overuse',
            'with_recursive' => 1,
        ]);
    }

    /**
     * Returns array of unit option, that are available for this price
     * depending on price type.
     *
     * @return array
     */
    public function getUnitOptions()
    {
        $unitGroup = [
            'speed' => ['bps', 'kbps', 'mbps', 'gbps', 'tbps'],
            'size' => ['mb', 'mb10', 'mb100', 'gb', 'tb'],
        ];

        $availableUnitsByPriceType = [
            'overuse,ip_num' => ['items'],
            'overuse,support_time' => ['hour'],
            'overuse,backup_du' => $unitGroup['size'],
            'overuse,server_traf_max' => $unitGroup['size'],
            'overuse,server_traf95_max' => $unitGroup['speed'],
            'overuse,server_du' => $unitGroup['size'],
            'overuse,server_ssd' => $unitGroup['size'],
            'overuse,server_sata' => $unitGroup['size'],
            'overuse,backup_traf' => $unitGroup['size'],
            'overuse,domain_traf' => $unitGroup['size'],
            'overuse,domain_num' => ['items'],
            'overuse,ip_traf_max' => $unitGroup['size'],
            'overuse,account_traf' => $unitGroup['size'],
            'overuse,account_du' => $unitGroup['size'],
            'overuse,mail_num' => ['items'],
            'overuse,mail_du' => $unitGroup['size'],
            'overuse,db_num' => ['items'],
        ];

        $units = Ref::getList('type,unit', 'hipanel.finance.units', [
            'with_recursive' => 1,
            'select' => 'oname_label',
            'mapOptions' => ['from' => 'oname'],
        ]);

        $possibleTypes = $availableUnitsByPriceType[$this->type] ?? [];

        return array_intersect_key($units, array_combine($possibleTypes, $possibleTypes));
    }

    /**
     * Method checks, whether current price quantity is predefined and is not a result
     * of sophisticated calculation on server side.
     * @return bool
     */
    public function isQuantityPredefined(): bool
    {
        if (!$this->isOveruse()
            && ($this->isShared() || $this->getSubtype() === 'rack_unit')
        ) {
            return false;
        }

        return true;
    }

    /**
     * @return bool Whether this price is shared
     */
    public function isShared(): bool
    {
        return $this->object_id === null;
    }

    public function getUnitLabel()
    {
        return $this->getUnitOptions()[$this->unit] ?? null;
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

    public function isOveruse()
    {
        return strpos($this->type, 'overuse,') === 0;
    }

    public function getSubtype()
    {
        [, $subtype] = explode(',', $this->type);

        return $subtype;
    }

    /**
     * {@inheritdoc}
     */
    public static function instantiate($row)
    {
        /** @var PriceModelFactory $factory */
        $factory = Yii::$container->get(PriceModelFactory::class);

        return $factory->build($row['class'] ?? 'SinglePrice', $row['type']);
    }

    public function formulaLines(): array
    {
        if (strlen($this->formula) === 0) {
            return [];
        }

        return explode("\n", $this->formula);
    }

    public function getMoney(): Money
    {
        // TODO: decide how to get MoneyParser correctly
        return Yii::$container->get(MoneyParser::class)
            ->parse((string)$this->price, strtoupper($this->currency));
    }
}
