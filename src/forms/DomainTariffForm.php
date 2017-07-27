<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\forms;

use hipanel\modules\finance\logic\IntegrityException;
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

    public function load($data, $formName = null)
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
    public function getParentServices()
    {
        return $this->createServices($this->parentTariff->resources);
    }

    /**
     * @param $resources
     * @return DomainService[]
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
            if (strcmp($resource->object_id, $id) === 0 && $resource->isTypeCorrect()) {
                $result[$resource->type] = $resource;
            }
        }

        if (empty($result)) {
            return [];
        }

        $types = $resource->getTypes();
        if (count($result) !== count($types)) {
            throw new IntegrityException('Found ' . count($result) . ' resources for zone "' . $zone . '". Must be exactly ' . count($types));
        }

        // sorts $result by order of $resource->getTypes()
        $result = array_merge($types, $result);

        return $result;
    }

    public function getZoneParentResources($zone)
    {
        $id = $this->zones[$zone];

        $result = [];

        foreach ($this->parentTariff->resources as $resource) {
            if (strcmp($resource->object_id, $id) === 0 && $resource->isTypeCorrect()) {
                $result[$resource->type] = $resource;
            }
        }

        if (empty($result)) {
            return [];
        }

        $types = $resource->getTypes();
        if (count($result) !== count($types)) {
            throw new IntegrityException('Found ' . count($result) . ' resources for zone "' . $zone . '". Must be exactly ' . count($types));
        }

        // sorts $result by order of $resource->getTypes()
        $result = array_merge($types, $result);

        return $result;
    }

    public function getZones()
    {
        return $this->zones;
    }

    public function setZones($zones)
    {
        $this->zones = array_flip($zones);
    }
}
