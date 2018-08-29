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

        $map = $this->priceModelFactory->getMap();
        foreach ($map as $formName => $className) {
            if (is_array($map[$formName])) {
                foreach ($map[$formName] as $type => $class) {
                    $class = (new \ReflectionClass($class))->getShortName();
                    if (isset($request[$class])) {
                        return true;
                    }
                }
            } elseif (isset($request[$formName])) {
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
        $usedClasses = [];

        $iter = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($this->priceModelFactory->getMap()));
        foreach ($iter as $className) {
            if (is_array($className) || isset($usedClasses[$className])) {
                continue;
            }
            $formName = (new \ReflectionClass($className))->getShortName();
            if (empty($request[$formName])) {
                continue;
            }

            $usedClasses[$className] = true;
            /** @var Price[] $models */
            $models = [];
            /** @var array $modelsData */
            $modelsData = [];
            /** @var Price $modelPrototype */
            $modelPrototype = $this->priceModelFactory->instantiate($className);
            $modelPrototype->setAttributes($this->modelOptions);
            $modelPrototype->scenario = $this->getScenario();

            $data = $request[$formName];
            foreach ($data as $key => $modelData) {
                $models[$key] = clone $modelPrototype;
                $modelsData[$modelPrototype->formName()][$key] = $modelData;
            }

            $modelPrototype->loadMultiple($models, $modelsData);
            $result = array_merge($result, $models);
        }

        return $result;
    }
}
