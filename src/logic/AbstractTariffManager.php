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
     * @var Tariff
     */
    public $baseTariff;

    /**
     * @var AbstractTariffForm
     */
    public $form;

    /**
     * @var string
     */
    public $scenario;

    /**
     * @var string The type used to find base tariff
     */
    protected $type;
    
    public function __construct($options = [])
    {
        $tariff = ArrayHelper::remove($options, 'tariff');

        parent::__construct($options);

        $this->findBaseModel();
        $this->createForm($tariff);
    }

    /**
     * Fills [[form]] property with a proper
     *
     * @param Tariff $tariff
     * @throws InvalidConfigException
     */
    protected function createForm($tariff = null)
    {
        throw new InvalidConfigException("Method createForm must be implemented");
    }

    protected function findBaseModel()
    {
        $availableTariffs = Tariff::perform('GetAvailableInfo', ['type' => $this->type], true);
        if (empty($availableTariffs)) {
            throw new ForbiddenHttpException('No available tariffs found');
        }

        $query = Tariff::find()->joinWith('resources')->prepare();
        $this->baseTariff = reset($query->populate($availableTariffs));
    }

    public function getType()
    {
        return $this->type;
    }
}
