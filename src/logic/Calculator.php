<?php

namespace hipanel\modules\finance\logic;

use hipanel\modules\finance\models\Calculation;
use hipanel\modules\finance\models\CalculableModelInterface;
use yii\base\Model;
use yii\web\UnprocessableEntityHttpException;

class Calculator
{
    /**
     * @var Model[]|CalculableModelInterface[]
     */
    protected $models;

    /**
     * @var Calculation[]
     */
    protected $calculations;

    /**
     * TariffCalculator constructor.
     * @param Model[] $models
     */
    public function __construct($models)
    {
        $this->models = $models;
    }

    /**
     * Gets [[Calculation]] for the $id
     *
     * @param integer $id
     * @return Calculation
     */
    public function getCalculation($id)
    {
        if ($this->calculations === null) {
            $this->execute();
        }

        return isset($this->calculations[$id]) ? $this->calculations[$id] : null;
    }

    /**
     * @return Calculation[]
     */
    public function getCalculations()
    {
        if ($this->calculations === null) {
            $this->execute();
        }

        return $this->calculations;
    }

    /**
     * @return \hipanel\modules\finance\models\Calculation[]
     * @throws UnprocessableEntityHttpException
     */
    public function execute()
    {
        $data = $this->collectData();

        if (empty($data)) {
            return [];
        }

        try {
            $rows = Calculation::perform('CalcValue', $data, true);
        } catch (\Exception $e) {
            throw new UnprocessableEntityHttpException('Failed to calculate value: ' . $e->getMessage(), 0, $e);
        }

        $this->calculations = $this->createCalculations($rows);

        return $this->calculations;
    }

    /**
     * @return array
     */
    protected function collectData()
    {
        $data = [];
        foreach ($this->models as $model) {
            $calculation = $model->getCalculationModel();
            $data[$calculation->calculation_id] = $calculation->toArray();
        }

        return $data;
    }

    /**
     * @param $rows
     * @return \hipanel\modules\finance\models\Calculation[]
     */
    private function createCalculations($rows)
    {
        $query = Calculation::find()->joinWith(['value'])->indexBy('calculation_id');
        $query->prepare();

        return $query->populate($rows);
    }
}
