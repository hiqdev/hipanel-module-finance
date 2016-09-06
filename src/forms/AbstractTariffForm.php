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
    /** @var Tariff */
    public $baseTariff;
    /** @var Tariff[] */
    public $baseTariffs;
    /** @var Tariff */
    protected $tariff;
    /** @var Resource[] */
    protected $_resources;

    public function init()
    {
        $this->initTariff();
    }

    protected function initTariff()
    {
        $this->selectBaseTariff();
        $this->ensureTariff();
        $this->ensureScenario();
    }

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

    protected function setDefaultTariff()
    {
        $this->setTariff($this->baseTariff);

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

    public function getResources()
    {
        return $this->_resources;
    }

    /**
     * @param array $resources
     * @throws InvalidConfigException when not implemented
     */
    public function setResources($resources)
    {
        throw new InvalidConfigException('Method "setResources" must be implemented');
    }

    public function getResourceTypes()
    {
        return reset($this->baseTariff->resources)->getTypes();
    }

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

    public function getBaseTariffsList()
    {
        return array_combine(
            ArrayHelper::getColumn($this->baseTariffs, 'id'),
            ArrayHelper::getColumn($this->baseTariffs, 'name')
        );
    }

    public function insert($runValidation = true, $attributes = null, $options = [])
    {
        throw new InvalidConfigException("Method load must be implemented");
    }

    public function update($runValidation = true, $attributes = null, $options = [])
    {
        throw new InvalidConfigException("Method load must be implemented");
    }

    /**
     * @return Tariff
     */
    public function getTariff()
    {
        return $this->tariff;
    }

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
}
