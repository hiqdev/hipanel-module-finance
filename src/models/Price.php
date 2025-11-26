<?php declare(strict_types=1);
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\models;

use hipanel\base\Model;
use hipanel\base\ModelTrait;
use hipanel\models\Ref;
use hipanel\modules\finance\models\factories\PriceModelFactory;
use hipanel\modules\finance\models\query\PriceQuery;
use hipanel\modules\finance\module\Price\Application\PriceUnitAvailabilityService;
use hipanel\modules\finance\module\Price\Domain\Collection\UnitCollectionInterface;
use hiqdev\hiart\ActiveQuery;
use Money\Money;
use Money\MoneyParser;
use Money\Currency;
use Yii;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/**
 * Class Price.
 *
 * @property int $id
 * @property int $plan_id
 * @property string|null $plan_type
 * @property string|int $object_id
 * @property string|float $price
 * @property string $currency
 * @property string $class
 * @property string|int $main_object_id
 * @property string $main_object_name
 * @property string $unit
 * @property string $type
 * @property string $quantity
 * @property string $formula
 * @property int|null parent_id
 * @property int $type_id
 *
 * @property TargetObject $object
 * @property Plan $plan
 * @property array|null $formula_lines
 * @property array|null $thresholds
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class Price extends Model implements RepresentablePrice
{
    use ModelTrait;

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_DELETE = 'delete';

    private const PROGRESSIVE_PRICE_CLASS = 'ProgressivePrice';

    public const TEMPLATE_PRICE_CLASS = 'TemplatePrice';

    public const SINGLE_PRICE_CLASS = 'SinglePrice';

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['id', 'parent_id', 'plan_id', 'object_id', 'type_id', 'unit_id', 'currency_id', 'main_object_id'], 'integer'],
            [['type', 'type_label', 'plan_type', 'plan_name', 'unit', 'currency', 'note', 'data', 'main_object_name'], 'string'],
            [['quantity', 'price'], 'number'],
            [['formula_lines'], 'safe'],
            [['class'], 'string'], // todo: probably, refactor is needed

            [['plan_id', 'type', 'currency'], 'required', 'on' => ['create', 'update']],
            [['price'], 'required', 'on' => ['create', 'update'], 'when' => function ($model) {
                return !$model->isCertificateType();
            }],
            [['sums'], 'each', 'rule' => ['required'], 'on' => ['create', 'update'], 'when' => function ($model) {
                return $model->isCertificateType();
            }],
            [['id'], 'required', 'on' => ['update', 'set-note', 'delete']],
            [
                ['class'],
                'default',
                'value' => function () {
                    return (new \ReflectionClass($this))->getShortName();
                },
            ],
            [['class'], 'string'],
            [['formula'], 'string', 'on' => ['create', 'update']], // TODO syn check
            [['thresholds', 'plan_type'], 'safe'],
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

    /**
     * @return string|null
     * @throws InvalidConfigException
     * @throws NotInstantiableException
     */
    public function getUnitLabel(): ?string
    {
        $unitOptions = $this->getUnitCollection()->toArray();

        return $unitOptions[$this->unit] ?? null;
    }

    /**
     * Returns array of unit option, that are available for this price
     * depending on price type.
     *
     * @return UnitCollectionInterface
     * @throws InvalidConfigException
     * @throws NotInstantiableException
     */
    public function getUnitCollection(): UnitCollectionInterface
    {
        $typeName = $this->type ?? '';
        $defaultUnit = $this->unit ?? '';
        $service = \Yii::$container->get(PriceUnitAvailabilityService::class);

        return $service->getAvailableUnitsForPrice($typeName, $defaultUnit);
    }

    public function getCurrencyOptions()
    {
        return Ref::getList('type,currency');
    }

    public function getObject()
    {
        return $this->hasOne(TargetObject::class, ['id' => 'id']);
    }

    public function getPlan(): ActiveQuery
    {
        return $this->hasOne(Plan::class, ['id' => 'plan_id']);
    }

    public static function tableName()
    {
        return Inflector::camel2id(StringHelper::basename(__CLASS__), '-');
    }

    public function isOveruse(): bool
    {
        return str_starts_with($this->type, 'overuse,');
    }

    public function isServer95Traf()
    {
        return false;
    }

    public function isCertificateType(?string $type = null): bool
    {
        $type ??= $this->type;
        if ($type === null) {
            return false;
        }

        return in_array($type, $this->getCertificateTypes(), true);
    }

    public function getSubtype(): string
    {
        [, $subtype] = explode(',', $this->type);

        return $subtype;
    }

    public function getCertificateTypes(): array
    {
        return [
            'certificate_purchase' => 'certificate,certificate_purchase',
            'certificate_renewal' => 'certificate,certificate_renewal',
        ];
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
            ->parse((string)$this->price, new Currency(strtoupper($this->currency)));
    }

    public function getFormulaLines(): array
    {
        return $this->formula_lines ?? [];
    }

    public static function find(array $options = []): PriceQuery
    {
        return new PriceQuery(get_called_class(), [
            'options' => $options,
        ]);
    }

    public function isRate(): bool
    {
        return static::class === RatePrice::class || str_starts_with($this->type, Plan::TYPE_REFERRAL);
    }

    public function isProgressive(): bool
    {
        return str_contains($this->type, 'overuse,');
    }

    public function getThresholds(): array
    {
        $thresholds = [];
        if (!empty($this->thresholds)) {
            foreach ($this->thresholds as $row) {
                $threshold = new Threshold($row);
                $threshold->setParent($this);
                $thresholds[] = $threshold;
            }
        }

        return empty($thresholds) ? [new Threshold()] : $thresholds;
    }

    public function setProgressivePricingThresholds(array $data): void
    {
        $this->setClass(self::PROGRESSIVE_PRICE_CLASS);
        $this->thresholds = $this->prepareThresholds($data);
    }

    private function prepareThresholds(array $data): array
    {
        foreach ($data as $key => $value) {
            $data[$key]['unit'] = $this->unit;
            $data[$key]['currency'] = $this->currency;
        }

        return $data;
    }

    public function hasProgressiveClass(): bool
    {
        return $this->class === self::PROGRESSIVE_PRICE_CLASS;
    }

    public function setClass(string $class): void
    {
        $this->setAttribute('class', $class);
    }
}
