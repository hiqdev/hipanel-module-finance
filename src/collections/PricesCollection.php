<?php declare(strict_types=1);
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\collections;

use Closure;
use hipanel\modules\finance\models\factories\PriceModelFactory;
use hipanel\modules\finance\models\Price;
use hipanel\modules\finance\models\Threshold;
use hiqdev\hiart\Collection;
use yii\web\Request;

/**
 * Class PricesCollection overrides loading behavior of parent class in order to:
 * make it possible to load all the models, specified in [[knownForms]] at once.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class PricesCollection extends Collection
{
    public function __construct(
        readonly private PriceModelFactory $priceModelFactory,
        readonly private Request $request,
        array $config = []
    )
    {
        parent::__construct($config);
        // Prevent default behavior in Collection::collectData() in order to allow all attributes for different models
        $this->dataCollector = fn(Price $model) => [$model->getPrimaryKey(), $model->toArray()];
    }

    public function load($data = null): PricesCollection|Collection
    {
        if ($data === null && $this->dataToBeLoadedExistsInPostRequest()) {
            $models = $this->createPriceModelsFromRequest();
            $this->handleProgressivePricing($models);
            $this->checkConsistency = false;

            return $this->set($models);
        }

        return parent::load($data);
    }

    public function collectData($attributes = null): array
    {
        $data = [];
        foreach ($this->models as $model) {
            if ($this->dataCollector instanceof Closure) {
                [$key, $row] = call_user_func($this->dataCollector, $model, $this);
            } else {
                $key = $model->getPrimaryKey();
                $row = $model->getAttributes($this->isConsistent() ? $attributes : $model->activeAttributes());
            }

            if ($key) {
                $data[$key] = $row;
            } else {
                $data[] = $row;
            }
        }

        return $data;
    }

    private function dataToBeLoadedExistsInPostRequest(): bool
    {
        $request = $this->request->post();

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

    private function createPriceModelsFromRequest(): array
    {
        /** @var Price[] $result */
        $result = [];
        $request = $this->request->post();
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
            $modelsData = [];
            /** @var Price $modelPrototype */
            $modelPrototype = $this->priceModelFactory->instantiate($className);
            $modelPrototype->setAttributes($this->modelOptions);
            $modelPrototype->scenario = $this->getScenario();

            $data = $request[$formName];
            foreach ($data as $key => $modelData) {
                if (empty(array_filter($modelData))) {
                    continue;
                }
                $models[$key] = clone $modelPrototype;
                $modelsData[$modelPrototype->formName()][$key] = $modelData;
            }

            $modelPrototype->loadMultiple($models, $modelsData);
            $result += $models;
        }

        return $result;
    }

    /**
     * @param Price[] $models
     * @return void
     */
    private function handleProgressivePricing(array $models): void
    {
        $progressiveData = $this->getProgressiveData();

        foreach ($models as $priceRowIdx => $model) {
            if ($model->isProgressive()) {
                if ($this->hasProgressiveDataForModel($progressiveData, $priceRowIdx)) {
                    $model->setProgressivePricingThresholds($progressiveData[$priceRowIdx]);
                } else if (!$model->isNewRecord) {
                    $this->rollbackProgressivePriceClassToPreviousClass($model);
                }
            }
        }
    }

    private function getProgressiveData(): array
    {
        return $this->request->post((new Threshold())->formName(), []);
    }

    private function hasProgressiveDataForModel(array $progressiveData, int $index): bool
    {
        return isset($progressiveData[$index]);
    }

    private function rollbackProgressivePriceClassToPreviousClass(Price $model): void
    {
        if ($model->hasProgressiveClass()) {
            $model->setClass($this->determinePreviousClass($model));
        }
    }

    private function determinePreviousClass(Price $model): string
    {
        return $model->plan_type === 'template' ? Price::TEMPLATE_PRICE_CLASS : Price::SINGLE_PRICE_CLASS;
    }
}
