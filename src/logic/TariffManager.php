<?php

namespace hipanel\modules\finance\logic;

use hipanel\modules\finance\models\Tariff;
use yii\base\InvalidConfigException;
use yii\web\ForbiddenHttpException;

abstract class TariffManager
{
    /**
     * @var string The type used to find base tariff
     */
    protected $type;

    /**
     * @var Tariff
     */
    public $baseModel;

    /**
     * @var Tariff
     */
    public $model;

    public function __construct($model = null)
    {
        $this->findBaseModel();
        $this->setModel($model);
    }

    protected function setModel($model = null)
    {
        throw new InvalidConfigException("Method findModel must be implemented");
    }

    protected function findBaseModel()
    {
        $availableTariffs = Tariff::perform('GetAvailableInfo', ['type' => $this->type], true);
        if (empty($availableTariffs)) {
            throw new ForbiddenHttpException('No available domain tariffs found');
        }

        $query = Tariff::find()->joinWith('resources')->prepare();
        $this->baseModel = reset($query->populate($availableTariffs));
    }

    public function getType()
    {
        return $this->type;
    }
}
