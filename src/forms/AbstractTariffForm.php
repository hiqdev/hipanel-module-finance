<?php

namespace hipanel\modules\finance\forms;

use hipanel\base\Model;
use hipanel\modules\finance\models\Tariff;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

abstract class AbstractTariffForm extends Model
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
    public $tariff;

    /** @var Tariff */
    public $baseTariff;

    /** @var Resource[] */
    protected $_resources;

    /** @inheritdoc */
    public function attributes()
    {
        return [
            'id',
            'parent_id',
            'name',
        ];
    }

    /** @inheritdoc */
    public function rules()
    {
        return [
            [['name'], 'safe'],
            [['parent_id', 'id'], 'integer']
        ];
    }

    /** @inheritdoc */
    public function fields()
    {
        return ArrayHelper::merge(array_combine($this->attributes(), $this->attributes()), [
            'resources' => '_resources'
        ]);
    }

    public function getResources()
    {
        return $this->_resources;
    }


    public function getResourceTypes()
    {
        return reset($this->baseTariff->resources)->getAvailableTypes();
    }

    public function attributeLabels()
    {
        return [
            'name' => Yii::t('hipanel/finance/tariff', 'Name'),
        ];
    }

    /**
     *
     * @param array $data to be loaded
     * @return bool
     * @throws InvalidConfigException when not implemented
     */
    public function load($data)
    {
        throw new InvalidConfigException("Method load must be implemented");
    }

    /**
     * @param array $data
     * @throws InvalidConfigException when not implemented
     */
    public function fill($data)
    {
        throw new InvalidConfigException("Method load must be implemented");
    }

    /**
     * @param array $resources
     * @throws InvalidConfigException when not implemented
     */
    public function setResources($resources)
    {
        throw new InvalidConfigException("Method load must be implemented");
    }

    public function insert($runValidation = true, $attributes = null, $options = [])
    {
        throw new InvalidConfigException("Method load must be implemented");
    }

    public function update($runValidation = true, $attributes = null, $options = [])
    {
        throw new InvalidConfigException("Method load must be implemented");
    }
}
