<?php

namespace hipanel\modules\finance\forms;

use hipanel\helpers\ArrayHelper;
use hipanel\modules\finance\models\DomainResource;
use hipanel\modules\finance\models\Tariff;
use Yii;

class DomainTariffForm extends \yii\base\Model
{
    public $id;
    public $name;
    public $parent_id;

    /** @var Tariff */
    public $model;

    /** @var Tariff */
    public $baseModel;

    protected $zones;

    protected $_resources;

    public function attributes()
    {
        return [
            'id',
            'parent_id',
            'name',
        ];
    }

    public function rules()
    {
        return [
            [['name'], 'safe'],
            [['parent_id', 'id'], 'integer']
        ];
    }

    public function fields()
    {
        return ArrayHelper::merge(parent::fields(), [
            'resources' => '_resources'
        ]);
    }

    /**
     * @param array $zones
     * @param Tariff $baseModel
     * @param Tariff $model
     * @return $this
     */
    public function fill($zones, Tariff $baseModel, Tariff $model = null)
    {
        $this->model = isset($model) ? $model : $baseModel;
        $this->baseModel = $baseModel;
        $this->zones = array_flip($zones);

        if (isset($model)) {
            $this->id = $this->model->id ?: null;
            $this->name = $this->model->name;
        }

        $this->parent_id = $this->baseModel->id;

        return $this;
    }

    public function load($data)
    {
        $this->setAttributes($data[$this->formName()]);
        $this->setResources($data[(new DomainResource())->formName()]);

        return true;
    }

    public function getResources()
    {
        return $this->_resources;
    }

    public function setResources($resources)
    {
        $result = [];
        foreach ($resources as $resource) {
            if ($resource instanceof DomainResource) {
                $result[] = $resource;
                continue;
            }

            $model = new DomainResource(['scenario' => 'create']);

            if ($model->load($resource, '') && $model->validate()) {
                $result[] = $model;
            } else {
                throw new \yii\web\UnprocessableEntityHttpException('Failed to load resource model');
            }
        }

        $this->_resources = $result;

        return $this;
    }

    public function getZoneResources($zone)
    {
        $id = $this->zones[$zone];

        $result = [];

        foreach ($this->model->resources as $resource) {
            if ($resource->object_id == $id && $resource->isTypeCorrect()) {
                $result[$resource->type] = $resource;
            }
        }

        // sorts $result by order of $resource->getAvailableTypes()
        $result = array_merge($resource->getAvailableTypes(), $result);

        return $result;
    }

    public function getZoneBaseResources($zone)
    {
        $id = $this->zones[$zone];

        $result = [];

        foreach ($this->baseModel->resources as $resource) {
            if ($resource->object_id == $id && $resource->isTypeCorrect()) {
                $result[$resource->type] = $resource;
            }
        }

        // sorts $result by order of $resource->getAvailableTypes()
        $result = array_merge($resource->getAvailableTypes(), $result);

        return $result;
    }

    public function getZones()
    {
        return $this->zones;
    }

    public function getResourceTypes()
    {
        return reset($this->baseModel->resources)->getAvailableTypes();
    }

    public function attributeLabels()
    {
        return [
            'name' => Yii::t('hipanel/finance/tariff', 'Название'),
        ];
    }
}
