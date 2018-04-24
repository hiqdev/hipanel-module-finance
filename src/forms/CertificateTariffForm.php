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
use Yii;
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

    /**
     * @return array
     */
    public static function getPeriods()
    {
        return CertificateResource::getPeriods();
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

    /**
     * @param $type
     * @return CertificateResource[]
     * @throws IntegrityException
     */
    public function getTypeResources($type)
    {
        return $this->extractResources($type, $this->tariff->resources);
    }

    /**
     * @param array $certificateTypes
     */
    public function setCertificateTypes($certificateTypes)
    {
        $this->certificateTypes = $certificateTypes;
    }

    /**
     * @param $type
     * @return CertificateResource[]
     * @throws IntegrityException
     */
    public function getTypeParentResources($type)
    {
        return $this->extractResources($type, $this->parentTariff->resources);
    }

    protected function extractResources($type, $resources)
    {
        $id = $this->getCertificateTypeId($type);

        $tmpres = [];

        foreach ($resources as $resource) {
            if (strcmp($resource->object_id, $id) === 0 && $resource->isTypeCorrect()) {
                $tmpres[$resource->type] = $resource;
            }
        }

        $types = $resource->getTypes();
        /* XXX why die? let's try with empty resource
         * if (count($tmpres) !== count($types)) {
            throw new IntegrityException('Found ' . count($tmpres) . ' resources for certificate "' . $type . '". Must be exactly ' . count($types));
        }

        // sorts $tmpres by order of $resource->getTypes()
        $tmpres = array_merge($types, $tmpres);
         */

        foreach (array_keys($types) as $type) {
            $res[$type] = isset($tmpres[$type]) ? $tmpres[$type] : new CertificateResource;
        }

        return $res;
    }
}
