<?php

namespace hipanel\modules\finance\logic;

use hipanel\base\Model;
use hipanel\modules\finance\models\Calculation;
use hipanel\modules\finance\models\CalculableModelInterface;
use yii\web\UnprocessableEntityHttpException;

class TariffCalculator
{
    /**
     * @var Model[]|CalculableModelInterface[]
     */
    private $tariffs;

    /**
     * @var Calculation[]
     */
    private $calculations;

    /**
     * TariffCalculator constructor.
     * @param Model[] $tariffs
     */
    public function __construct($tariffs)
    {
        $this->tariffs = $tariffs;
    }

    /**
     * Gets [[Calculation]] for the $tariffId
     *
     * @param integer $tariffId
     * @return Calculation
     */
    public function getCalculation($tariffId)
    {
        if ($this->calculations === null) {
            $this->execute();
        }

        return $this->calculations[$tariffId];
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
        try {
            $rows = Calculation::perform('CalcValue', $this->collectData(), true);
        } catch (\Exception $e) {
            throw new UnprocessableEntityHttpException('Failed to calculate tariffs value', 0, $e);
        }

        $this->calculations = $this->createCalculations($rows);

        return $this->calculations;
    }

    /**
     * @return array
     */
    private function collectData()
    {
        $data = [];
        foreach ($this->tariffs as $tariff) {
            $calculation = $tariff->getCalculationModel();
            $data[$tariff->getPrimaryKey()] = $calculation->getAttributes();
        }

        return $data;
    }

    /**
     * @param $rows
     * @return \hipanel\modules\finance\models\Calculation[]
     */
    private function createCalculations($rows)
    {
        $query = Calculation::find()->joinWith(['value'])->indexBy('tariff_id');
        $query->prepare();

        return $query->populate($rows);
    }
}
