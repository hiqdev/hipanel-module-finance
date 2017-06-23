<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\logic;

use hipanel\modules\finance\forms\AbstractTariffForm;
use hipanel\modules\finance\models\Tariff;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Object;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

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
        if (!isset($this->type)) {
            throw new InvalidConfigException('Property "type" must be set');
        }

        $this->fillParentTariffs();
        $this->buildForm();
    }

    /**
     * Fills [[form]] property with a proper [[AbstractTariffForm]] object.
     */
    protected function buildForm()
    {
        $this->form = Yii::createObject(array_merge([
            'scenario' => $this->scenario,
            'parentTariffs' => $this->parentTariffs,
            'tariff' => $this->tariff,
        ], $this->getFormOptions()));
    }

    protected function getFormOptions()
    {
        return $this->formOptions;
    }

    protected function fillParentTariffs()
    {
        $ids = $this->collectParentTariffIds();

        $this->parentTariffs = Tariff::find()
            ->where(['id' => $ids])
            ->details()
            ->all();
    }

    /**
     * Collects parent tariff ids. Used in [[fillParentTariffs]]
     *
     * @return array
     * @throws NotFoundHttpException
     */
    protected function collectParentTariffIds()
    {
        if (!isset($this->tariff)) {
            $availableTariffs = Tariff::find()
                ->action('get-available-info')
                ->andFilterWhere(['type' => $this->type])
                ->all();

            return ArrayHelper::getColumn($availableTariffs, 'id');
        }

        if (isset($this->tariff->parent_id)) {
            return [$this->tariff->parent_id];
        }

        return [$this->tariff->id];
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
