<?php

namespace hipanel\modules\finance\forms;

use hipanel\modules\finance\logic\Calculator;
use hipanel\modules\finance\models\Tariff;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

abstract class AbstractTariffForm extends \yii\base\Model
{
    /**
     * @var int Tariff ID
     */
    public $id;

    /**
     * @var string Tariff name
     */
    public $name;

    /**
     * @var int Parent tariff ID
     */
    public $parent_id;

    /**
     * @var Tariff[] array of available parent tariffs
     */
    public $parentTariffs;

    /**
     * @var Tariff the selected parent tariff
     */
    public $parentTariff;

    /**
     * @var Tariff
     */
    protected $tariff;

    /**
     * @var \hipanel\modules\finance\models\Resource[]
     */
    protected $_resources;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (!isset($this->parentTariffs)) {
            throw new InvalidConfigException('Property "parentTariffs" must be filled');
        }

        $this->initTariff();
    }

    /**
     * Initializes tariff
     * @void
     */
    protected function initTariff()
    {
        $this->selectParentTariff();
        $this->ensureTariff();
        $this->ensureScenario();
    }

    /**
     * Ensures that [[tariff]] is set.
     * Otherwise calls [[setDefaultTariff()]]
     * @return bool
     */
    protected function ensureTariff()
    {
        if ($this->getTariff() instanceof Tariff) {
            return true;
        }

        return $this->setDefaultTariff();
    }

    protected function ensureScenario()
    {
        foreach ($this->tariff->resources as $resource) {
            $resource->scenario = $this->scenario;
        }
    }

    /**
     * Sets default tariff
     *
     * @return bool
     */
    protected function setDefaultTariff()
    {
        $this->setTariff($this->parentTariff);

        // Default tariff's id and name are useless on create
        $this->id = null;
        $this->name = null;

        return true;
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            [['name'], 'required', 'on' => ['create', 'update']],
            [['parent_id', 'id'], 'integer', 'on' => ['create', 'update']],
            [['parent_id'], 'required', 'on' => ['create']],
            [['id'], 'required', 'on' => ['update']],
        ];
    }

    /** @inheritdoc */
    public function fields()
    {
        return ArrayHelper::merge(array_combine($this->attributes(), $this->attributes()), [
            'resources' => '_resources'
        ]);
    }

    /** @inheritdoc */
    public function attributes()
    {
        return [
            'id',
            'parent_id',
            'name',
        ];
    }

    /**
     * @return \hipanel\modules\finance\models\Resource[]
     */
    public function getResources()
    {
        return $this->_resources;
    }

    /**
     * @param \hipanel\modules\finance\models\Resource[] $resources
     * @throws InvalidConfigException when not implemented
     */
    public function setResources($resources)
    {
        throw new InvalidConfigException('Method "setResources" must be implemented');
    }

    /**
     * @return array
     */
    public function getResourceTypes()
    {
        return reset($this->parentTariff->resources)->getTypes();
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'parent_id' => Yii::t('hipanel:finance:tariff', 'Parent tariff'),
            'name' => Yii::t('hipanel:finance:tariff', 'Name'),
            'label' => Yii::t('hipanel:finance:tariff', 'Label'),
            'note' => Yii::t('hipanel', 'Note'),
        ];
    }

    /**
     * @param array $data to be loaded
     * @param null $formName
     * @return bool
     * @throws InvalidConfigException when not implemented
     */
    public function load($data, $formName = null)
    {
        throw new InvalidConfigException("Method load must be implemented");
    }

    /**
     * Selects one of [[parentTariffs]] and puts it to [[parentTariff]]
     *
     * @return bool
     */
    public function selectParentTariff()
    {
        if (!isset($this->parent_id)) {
            if (isset($this->tariff)) {
                $this->parent_id = $this->tariff->parent_id;
            } else {
                $this->parent_id = ArrayHelper::getValue(reset($this->parentTariffs), 'id');
            }
        }

        $filtered = array_filter($this->parentTariffs, function ($model) {
            return $model->id == $this->parent_id;
        });

        if (count($filtered) !== 1) {
            Yii::error('Found ' . count($filtered) . ' parent tariffs. Must be exactly one');
            return false;
        }

        $this->parentTariff = reset($filtered);
        $this->parent_id = $this->parentTariff->id;

        return true;
    }

    /**
     * Builds key-value array of [[parentTariffs]]
     *  - key: tariff id
     *  - value: tariff name
     *
     * @return array
     */
    public function getParentTariffsList()
    {
        return array_combine(
            ArrayHelper::getColumn($this->parentTariffs, 'id'),
            ArrayHelper::getColumn($this->parentTariffs, 'name')
        );
    }

    public function insert($runValidation = true)
    {
        throw new InvalidConfigException("Method insert must be implemented");
    }

    public function update($runValidation = true)
    {
        throw new InvalidConfigException("Method update must be implemented");
    }

    /**
     * @return Tariff
     */
    public function getTariff()
    {
        return $this->tariff;
    }

    /**
     * Sets [[tariff]]
     *
     * @param Tariff $tariff
     * @return bool
     */
    public function setTariff($tariff)
    {
        if ($tariff === null) {
            return false;
        }

        $this->tariff = $tariff;

        $this->id = $tariff->id;
        $this->name = $tariff->name;

        return true;
    }

    public function getPrimaryKey()
    {
        return ['id'];
    }

    /**
     * @var Calculator
     */
    protected $_calculator;

    /**
     * Creates [[TariffCalculator]] object for the [[tariff]]
     *
     * @return Calculator
     */
    protected function calculator()
    {
        if (isset($this->_calculator)) {
            return $this->_calculator;
        }

        $this->_calculator = new Calculator([$this->tariff]);

        return $this->_calculator;
    }

    /**
     * @return \hipanel\modules\finance\models\Value
     */
    public function calculation()
    {
        return $this->calculator()->getCalculation($this->tariff->id)->forCurrency($this->tariff->currency);
    }

    /**
     * @var Calculator
     */
    protected $_parentCalculator;

    /**
     * Creates [[TariffCalculator]] object for the [[parentTariff]]
     *
     * @return Calculator
     */
    protected function parentCalculator()
    {
        if (isset($this->_parentCalculator)) {
            return $this->_parentCalculator;
        }

        $this->_parentCalculator = new Calculator([$this->parentTariff]);

        return $this->_parentCalculator;
    }

    /**
     * @return \hipanel\modules\finance\models\Value
     */
    public function parentCalculation()
    {
        return $this->parentCalculator()->getCalculation($this->parentTariff->id)->forCurrency($this->parentTariff->currency);
    }
}
