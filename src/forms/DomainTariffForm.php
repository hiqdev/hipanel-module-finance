<?php

namespace hipanel\modules\finance\forms;

use hipanel\modules\finance\models\DomainResource;
use hipanel\modules\finance\models\DomainService;
use hipanel\modules\finance\models\Tariff;
use yii\web\UnprocessableEntityHttpException;

class DomainTariffForm extends AbstractTariffForm
{
    /**
     * @var array Domain zones
     * Key - zone name (com, net, ...)
     * Value - zone id
     * @see getZones
     */
    protected $zones;

    public function load($data)
    {
        $this->setAttributes($data[$this->formName()]);
        $this->setResources($data[(new DomainResource())->formName()]);

        $this->initTariff();

        return true;
    }

    /**
     * @return DomainService[]
     */
    public function getServices()
    {
        return $this->createServices($this->tariff->resources);
    }

    /**
     * @return DomainService[]
     */
    public function getBaseServices()
    {
        return $this->createServices($this->baseTariff->resources);
    }

    /**
     * @param $resources
     * @return DomainService[]
     *
     */
    protected function createServices($resources)
    {
        $result = [];
        $resource = reset($resources);

        foreach ($resource->getServiceTypes() as $type => $name) {
            $service = new DomainService([
                'name' => $name,
                'type' => $type,
            ]);

            foreach ($resources as $resource) {
                if ($service->tryResourceAssignation($resource) && $service->isFulfilled()) {
                    $result[$type] = $service;
                    break;
                }
            }
        }

        return $result;
    }

    public function setResources($resources)
    {
        $result = [];
        foreach ($resources as $resource) {
            if ($resource instanceof DomainResource) {
                $result[] = $resource;
                continue;
            }

            $model = new DomainResource(['scenario' => $this->scenario]);

            if ($model->load($resource, '') && $model->validate()) {
                $result[] = $model;
            } else {
                throw new UnprocessableEntityHttpException('Failed to load resource model: ' . reset($model->getFirstErrors()));
            }
        }

        $this->_resources = $result;

        return $this;
    }

    public function getZoneResources($zone)
    {
        $id = $this->zones[$zone];

        $result = [];

        foreach ($this->tariff->resources as $resource) {
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

        foreach ($this->baseTariff->resources as $resource) {
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

    public function setZones(array $zones)
    {
        $this->zones = array_flip($zones);
    }
}
