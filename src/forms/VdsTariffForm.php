<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\forms;

use hipanel\helpers\ArrayHelper;
use hipanel\modules\finance\models\ServerResource;
use yii\web\UnprocessableEntityHttpException;

class VdsTariffForm extends AbstractTariffForm
{
    public $note;
    public $label;

    public function load($data, $formName = null)
    {
        $this->setAttributes($data[$this->formName()]);
        $this->setResources($data[(new ServerResource())->formName()]);

        return true;
    }

    public function rules()
    {
        $rules = parent::rules();
        $rules[] = [['note', 'label'], 'safe', 'on' => ['create', 'update']];

        return $rules;
    }

    /**
     * @return \hipanel\modules\finance\models\ServerResource[]
     */
    public function getHardwareResources()
    {
        /** @var ServerResource[] $resources */
        $resources = array_filter($this->tariff->resources, function ($model) {
            /** @var ServerResource $model */
            return $model->isHardwareTypeCorrect();
        });

        if (empty($resources)) {
            return [];
        }

        $order = array_keys(reset($resources)->getHardwareTypes());

        return $this->sortResourcesByDefinedOrder($resources, $order, 'model_type');
    }

    /**
     * @param ServerResource[] $resources
     * @param array $order array of ordered values. $resources array will be re-ordered according this order
     * @param string $key the key that will be used to re-order
     * @return array
     */
    private function sortResourcesByDefinedOrder($resources, $order, $key)
    {
        $result = [];
        $resources = ArrayHelper::index($resources, $key);

        foreach ($order as $type) {
            if (isset($resources[$type])) {
                $result[] = $resources[$type];
            }
        }

        return $result;
    }

    /**
     * @return \hipanel\modules\finance\models\ServerResource[]
     */
    public function getOveruseResources()
    {
        /** @var ServerResource[] $resources */
        $resources = array_filter($this->tariff->resources, function ($model) {
            /** @var ServerResource $model */
            return $model->isTypeCorrect();
        });
        if (empty($resources)) {
            return [];
        }

        $order = array_keys(reset($resources)->getTypes());

        return $this->sortResourcesByDefinedOrder($resources, $order, 'type');
    }

    public function getParentOveruseResource($type_id)
    {
        return reset(array_filter($this->parentTariff->resources, function ($resource) use ($type_id) {
            /** @var ServerResource $resource */
            return strcmp($resource->type_id, $type_id) === 0 && $resource->isTypeCorrect();
        }));
    }

    /**
     * @return \hipanel\modules\finance\models\ServerResource[]
     */
    public function getParentHardwareResource($object_id)
    {
        return reset(array_filter($this->parentTariff->resources, function ($resource) use ($object_id) {
            /** @var ServerResource $resource */
            return strcmp($resource->object_id, $object_id) === 0 && $resource->isHardwareTypeCorrect();
        }));
    }

    /** {@inheritdoc} */
    public function setResources($resources)
    {
        $result = [];
        foreach ((array) $resources as $resource) {
            if ($resource instanceof ServerResource) {
                $result[] = $resource;
                continue;
            }

            $model = new ServerResource(['scenario' => $this->scenario]);

            if ($model->load($resource, '') && $model->validate()) {
                $result[] = $model;
            } else {
                throw new UnprocessableEntityHttpException('Failed to load resource model: ' . reset($model->getFirstErrors()));
            }
        }

        $this->_resources = $result;

        return $this;
    }
}
