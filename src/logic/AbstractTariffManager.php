<?php

namespace hipanel\modules\finance\logic;

use hipanel\modules\finance\forms\AbstractTariffForm;
use hipanel\modules\finance\models\Tariff;
use yii\base\InvalidConfigException;
use yii\base\Object;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;

abstract class AbstractTariffManager extends Object
{
    /**
     * @var Tariff[] array of all available base tariffs
     */
    protected $baseTariffs;

    /**
     * @var AbstractTariffForm
     */
    public $form;

    /**
     * @var string
     */
    public $scenario;

    /**
     * @var Tariff The actual tariff
     */
    protected $tariff;

    /**
     * @var string The type used to find base tariff
     */
    protected $type;
    
    public function init()
    {
        $this->findBaseTariffs();
        $this->buildForm();
    }

    /**
     * Fills [[form]] property with a proper [[AbstractTariffForm]] object
     */
    protected function buildForm()
    {
        throw new InvalidConfigException('Method "createForm" must be implemented');
    }

    protected function findBaseTariffs()
    {
        $availableTariffs = Tariff::perform('GetAvailableInfo', ['type' => $this->type], true);

        if (empty($availableTariffs)) {
            throw new ForbiddenHttpException('No available tariffs found');
        }

        $this->baseTariffs = Tariff::find()
            ->where(['id' => array_keys($availableTariffs)])
            ->details()
            ->all();
    }

    public function getType()
    {
        return $this->type;
    }

    /**
     * @param Tariff $tariff
     */
    public function setTariff($tariff)
    {
        $this->tariff = $tariff;
    }
}
