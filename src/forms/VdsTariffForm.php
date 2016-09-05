<?php

namespace hipanel\modules\finance\forms;

use hipanel\modules\finance\models\DomainResource;
use hipanel\modules\finance\models\ServerResource;
use hipanel\modules\server\models\Package;

class VdsTariffForm extends AbstractTariffForm
{
    public $note;
    public $label;

    protected $package;

    public function load($data)
    {
        $this->setAttributes($data[$this->formName()]);
        $this->setResources($data[(new DomainResource())->formName()]);

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
        return array_filter($this->tariff->resources, function ($model) {
            /** @var ServerResource $model */
            return $model->isModelTypeCorrect();
        });
    }

    /**
     * @return \hipanel\modules\finance\models\ServerResource[]
     */
    public function getOveruseResources()
    {
        return array_filter($this->tariff->resources, function ($model) {
            /** @var ServerResource $model */
            return $model->isTypeCorrect();
        });
    }

    public function getBaseResource($object_id)
    {
        return reset(array_filter($this->baseTariff->resources, function ($resource) use ($object_id) {
            return $resource->object_id == $object_id && $resource->isModelTypeCorrect();
        }));
    }

    public function getBaseOveruseResource($type_id)
    {
        return reset(array_filter($this->baseTariff->resources, function ($resource) use ($type_id) {
            return $resource->type_id == $type_id && $resource->isTypeCorrect();
        }));
    }

    /**
     * @return \hipanel\modules\finance\models\ServerResource[]
     */
    public function getBaseHardwareResource($object_id)
    {
        return reset(array_filter($this->baseTariff->resources, function ($resource) use ($object_id) {
            /** @var ServerResource $model */
            return $resource->object_id == $object_id && $resource->isModelTypeCorrect();
        }));
    }

    public function getPackage()
    {
        if (!$this->package instanceof Package) {
            $this->package = new Package([
                'tariff' => $this->tariff,
            ]);
        }

        return $this->package;
    }
}
