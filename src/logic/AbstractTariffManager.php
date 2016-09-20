<?php

namespace hipanel\modules\finance\logic;

use hipanel\modules\finance\forms\AbstractTariffForm;
use hipanel\modules\finance\models\Tariff;
use Yii;
use yii\base\Object;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;

abstract class AbstractTariffManager extends Object
{
    /**
     * @var Tariff[] they array of all available parent tariffs
     * @see findParentTariffs()
     */
    protected $parentTariffs;

    /**
     * @var AbstractTariffForm
     */
    public $form;

    /**
     * @var array options used to build [[form]]
     * @see buildForm()
     */
    public $formOptions = [];

    /**
     * @var string
     */
    public $scenario;

    /**
     * @var Tariff The actual tariff
     */
    protected $tariff;

    /**
     * @var string The type used to find parent tariffs
     */
    protected $type;
    
    public function init()
    {
        $this->findParentTariffs();
        $this->buildForm();
    }

    /**
     * Fills [[form]] property with a proper [[AbstractTariffForm]] object
     */
    protected function buildForm()
    {
        $this->form = Yii::createObject(array_merge([
            'scenario' => $this->scenario,
            'parentTariffs' => $this->parentTariffs,
            'tariff' => $this->tariff
        ], $this->getFormOptions()));
    }

    protected function getFormOptions()
    {
        return $this->formOptions;
    }

    protected function findParentTariffs()
    {
        $availableTariffs = Tariff::find(['scenario' => 'get-available-info'])
            ->andFilterWhere(['type' => $this->type])
            ->all();

        if (empty($availableTariffs)) {
            throw new ForbiddenHttpException('No available tariffs found');
        }

        $this->parentTariffs = Tariff::find()
            ->where(['id' => ArrayHelper::getColumn($availableTariffs, 'id')])
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
