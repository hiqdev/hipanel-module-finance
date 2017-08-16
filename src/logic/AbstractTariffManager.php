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
use yii\base\BaseObject;

abstract class AbstractTariffManager extends BaseObject
{
    /**
     * @var int Parent tariff ID
     */
    public $parent_id;

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
     * @var Tariff Parent tariff
     */
    protected $parentTariff;

    /**
     * @var string The type used to find parent tariffs
     */
    protected $type;

    public function init()
    {
        if (!isset($this->type)) {
            throw new InvalidConfigException('Property "type" must be set');
        }

        $this->initParentTariff();
        $this->buildForm();
    }

    /**
     * Fills [[form]] property with a proper [[AbstractTariffForm]] object.
     */
    protected function buildForm()
    {
        $this->form = Yii::createObject(array_merge([
            'scenario' => $this->scenario,
            'parent_id' => $this->parent_id,
            'parentTariff' => $this->parentTariff,
            'tariff' => $this->tariff,
        ], $this->getFormOptions()));
    }

    protected function getFormOptions()
    {
        return $this->formOptions;
    }

    protected function initParentTariff()
    {
        $id = $this->determineParentTariff();

        if ($id === null) {
            return;
        }

        $this->parent_id = $id;
        $this->parentTariff = Tariff::find()
            ->where(['id' => $id])
            ->details()
            ->one();

        // In case parent tariff is not available - use current tariff as the last hope fallback
        if ($this->parentTariff === null && $this->tariff !== null) {
            $this->parentTariff = clone $this->tariff;
        }
    }

    /**
     * Finds parent tariff ID
     *
     * @return int
     */
    protected function determineParentTariff()
    {
        if ($this->tariff === null) {
            if (!empty($this->parent_id)) {
                return $this->parent_id;
            }

            return null;
        }

        if ($this->tariff->parent_id !== null) {
            return $this->tariff->parent_id;
        }

        return $this->tariff->id;
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
