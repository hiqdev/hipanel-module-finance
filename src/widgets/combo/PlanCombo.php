<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\widgets\combo;

use hiqdev\combo\Combo;
use yii\helpers\ArrayHelper;

class PlanCombo extends Combo
{
    /** {@inheritdoc} */
    public $type = 'plan/name';

    /** {@inheritdoc} */
    public $name = 'plan';

    /** {@inheritdoc} */
    public $url = '/finance/plan/index';

    /** {@inheritdoc} */
    public $_return = ['id'];

    /** {@inheritdoc} */
    public $_rename = ['text' => 'plan'];

    public $_primaryFilter = 'plan_ilike';

    /**
     * @var array|string[] the type of tariff
     * @see getFilter()
     */
    public $tariffType;

    /**
     * @var array|string[] map target type to the tariff type used in the API
     * // TODO: Replace with Billing Registry and some kind of Behavior.
     */
    private array $tariffTypeAlias = [
        'switch_license' => 'switch',
    ];

    /** {@inheritdoc} */
    public function getFilter()
    {
        return ArrayHelper::merge(parent::getFilter(), [
            'type_in' => [
                'format' => $this->resolveTariffType(),
            ],
        ]);
    }

    /**
     * @return string|string[]
     */
    private function resolveTariffType()
    {
        if (is_string($this->tariffType) && $this->isAlias($this->tariffType)) {
            return $this->tariffTypeAlias[$this->tariffType];
        }

        return $this->tariffType;
    }

    private function isAlias(string $type): bool
    {
        return isset($this->tariffTypeAliases[$type]);
    }
}
