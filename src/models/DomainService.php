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

use Yii;
use yii\base\Model;

class DomainService extends Model
{
    const SERVICE_OPERATION_PURCHASE = 'purchase';
    const SERVICE_OPERATION_RENEW = 'renew';

    /**
     * @var string Human-readable name
     */
    public $name;

    /**
     * @var string Service type
     */
    public $type;

    /** @var DomainResource[] */
    public $resources;

    /**
     * Returns resource for $operation.
     *
     * @param string $operation
     * @return DomainResource
     */
    public function getResource($operation)
    {
        return $this->resources[$operation];
    }

    /**
     * Tries to assign a resource in this service, if the type is correct.
     *
     * @param DomainResource $resource
     * @return bool
     */
    public function tryResourceAssignation(DomainResource $resource)
    {
        if ($type = $this->matchType($resource)) {
            $this->resources[$type] = $resource;

            return true;
        }

        return false;
    }

    /**
     * Check whether $resource belongs to this service type.
     *
     * @param DomainResource $resource
     * @return false|string operation type or false, if resource does not match this service
     */
    protected function matchType(DomainResource $resource)
    {
        foreach ($this->getOperations() as $operation => $name) {
            if ($resource->type === $this->type . "_$operation") {
                return $operation;
            }
        }

        return false;
    }

    /**
     * @return array available operations
     */
    public static function getOperations()
    {
        return [
            static::SERVICE_OPERATION_PURCHASE => Yii::t('hipanel:finance:tariff', 'Purchase'),
            static::SERVICE_OPERATION_RENEW => Yii::t('hipanel:finance:tariff', 'Renewal'),
        ];
    }

    /**
     * @return bool whether service contains all necessary resources
     */
    public function isFulfilled()
    {
        return count($this->resources) === count($this->getOperations());
    }
}
