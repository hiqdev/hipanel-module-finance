<?php

namespace hipanel\modules\finance\collections;

use hipanel\modules\finance\models\Price;
use hipanel\modules\finance\models\factories\PriceModelFactory;
use hiqdev\hiart\Collection;
use Yii;

/**
 * Class PricesCollection overrides loading behavior of parent class in order to:
 * make it possible to load all the models, specified in [[knownForms]] at once
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class PricesCollection extends Collection
{
    /**
     * @var \hipanel\modules\finance\models\factories\PriceModelFactory
     */
    private $priceModelFactory;

    public function __construct(PriceModelFactory $priceModelFactory, array $config = [])
    {
        parent::__construct($config);
        $this->priceModelFactory = $priceModelFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function load($data = null)
    {
        if ($data === null && $this->dataToBeLoadedExistsInPostRequest()) {
            $data = $this->loadDifferentModelsFromPostRequest();
            $this->checkConsistency = false;

            return $this->set($data);
        }

        return parent::load($data);
    }

    private function dataToBeLoadedExistsInPostRequest()
    {
        $request = Yii::$app->request->post();

        foreach ($this->priceModelFactory->getMap() as $formName => $className) {
            if (isset($request[$formName])) {
                return true;
            }
        }

        return false;
    }

    private function loadDifferentModelsFromPostRequest()
    {
        /** @var Price[] $result */
        $result = [];
        $request = Yii::$app->request->post();

        foreach ($this->priceModelFactory->getMap() as $formName => $className) {
            if (empty($request[$formName])) {
                continue;
            }

            /** @var Price[] $models */
            $models = [];
            /** @var array $modelsData */
            $modelsData = [];
            /** @var Price $modelPrototype */
            $modelPrototype = $this->priceModelFactory->build($formName);
            $modelPrototype->setAttributes($this->modelOptions);

            $data = $request[$formName];
            foreach ($data as $key => $modelData) {
                $models[$key] = clone $modelPrototype;
                $modelsData[$modelPrototype->formName()][$key] = $modelData;
            }

            $modelPrototype->loadMultiple($models, $modelsData);
            $result += $models;
        }

        return $result;
    }
}
