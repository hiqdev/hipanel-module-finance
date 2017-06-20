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
use hipanel\modules\finance\models\CertificateResource;
use yii\web\UnprocessableEntityHttpException;

/**
 * Class CertificateTariffForm
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class CertificateTariffForm extends AbstractTariffForm
{
    protected $certificateTypes = [];

    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    public function load($data, $formName = null)
    {
        $this->setAttributes($data[$this->formName()]);
        $this->setResources($data[(new CertificateResource())->formName()]);

        $this->initTariff();

        return true;
    }

    public function setResources($resources)
    {
        $result = [];
        foreach ($resources as $resource) {
            if ($resource instanceof CertificateResource) {
                $result[] = $resource;
                continue;
            }

            $model = new CertificateResource(['scenario' => $this->scenario]);

            if ($model->load($resource, '') && $model->validate()) {
                $result[] = $model;
            } else {
                throw new UnprocessableEntityHttpException('Failed to load resource model: ' . reset($model->getFirstErrors()));
            }
        }

        $this->_resources = $result;

        return $this;
    }

    public function getCertificateTypes()
    {
        $result = [];

        foreach ($this->tariff->resources as $resource) {
            if (isset($this->certificateTypes[$resource->object_id])) {
                $result[$resource->object_id] = $this->certificateTypes[$resource->object_id];
            }
        }

        return $result;
    }

    protected function getCertificateTypeId($type)
    {
        return array_search($type, $this->certificateTypes, true);
    }

    public function getTypeResources($type)
    {
        $id = $this->getCertificateTypeId($type);

        $result = [];

        foreach ($this->tariff->resources as $resource) {
            if (strcmp($resource->object_id, $id) === 0 && $resource->isTypeCorrect()) {
                $result[$resource->type] = $resource;
            }
        }

        $types = $resource->getTypes();
        if (count($result) !== count($types)) {
            throw new IntegrityException('Found ' . count($result) . ' resources for certificate "' . $type . '". Must be exactly ' . count($types));
        }

        // sorts $result by order of $resource->getTypes()
        $result = array_merge($types, $result);

        return $result;
    }

    /**
     * @param array $certificateTypes
     */
    public function setCertificateTypes($certificateTypes)
    {
        $this->certificateTypes = $certificateTypes;
    }

    public function getTypeParentResources($certificateType)
    {
        $id = $this->getCertificateTypeId($certificateType);

        $result = [];

        foreach ($this->parentTariff->resources as $resource) {
            if (strcmp($resource->object_id, $id) === 0 && $resource->isTypeCorrect()) {
                $result[$resource->type] = $resource;
            }
        }

        $types = $resource->getTypes();
        if (count($result) !== count($types)) {
            throw new IntegrityException('Found ' . count($result) . ' resources for certificate "' . $type . '". Must be exactly ' . count($types));
        }

        // sorts $result by order of $resource->getTypes()
        $result = array_merge($types, $result);

        return $result;
    }
}
