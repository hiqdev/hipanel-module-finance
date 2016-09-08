<?php

namespace hipanel\modules\finance\forms;

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
     * @var Tariff[] array of available base tariffs
     */
    public $baseTariffs;

    /**
     * @var Tariff the selected base tariff
     */
    public $baseTariff;

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
        if (!isset($this->baseTariffs)) {
            throw new InvalidConfigException('Property "baseTariffs" must be filled');
        }

        $this->initTariff();
    }

    /**
     * Initializes tariff
     * @void
     */
    protected function initTariff()
    {
        $this->selectBaseTariff();
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
        $this->setTariff($this->baseTariff);

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
        return reset($this->baseTariff->resources)->getTypes();
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'parent_id' => Yii::t('hipanel/finance/tariff', 'Parent tariff'),
            'name' => Yii::t('hipanel/finance/tariff', 'Name'),
            'label' => Yii::t('hipanel/finance/tariff', 'Label'),
            'note' => Yii::t('hipanel', 'Note'),
        ];
    }

    /**
     * @param array $data to be loaded
     * @return bool
     * @throws InvalidConfigException when not implemented
     */
    public function load($data)
    {
        throw new InvalidConfigException("Method load must be implemented");
    }

    /**
     * Selects one of [[baseTariffs]] and puts it to [[baseTariff]]
     *
     * @return bool
     */
    public function selectBaseTariff()
    {
        if (!isset($this->parent_id)) {
            if (isset($this->tariff)) {
                $this->parent_id = $this->tariff->parent_id;
            } else {
                $this->parent_id = ArrayHelper::getValue(reset($this->baseTariffs), 'id');
            }
        }

        $filtered = array_filter($this->baseTariffs, function ($model) {
            return $model->id == $this->parent_id;
        });

        if (count($filtered) !== 1) {
            Yii::error('Found ' . count($filtered) . ' base tariffs. Must be exactly one');
            return false;
        }

        $this->baseTariff = reset($filtered);
        $this->parent_id = $this->baseTariff->id;

        return true;
    }

    /**
     * Builds key-value array of [[baseTariffs]]
     *  - key: tariff id
     *  - value: tariff name
     *
     * @return array
     */
    public function getBaseTariffsList()
    {
        return array_combine(
            ArrayHelper::getColumn($this->baseTariffs, 'id'),
            ArrayHelper::getColumn($this->baseTariffs, 'name')
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
}
