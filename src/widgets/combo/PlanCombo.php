<?php

/*
 * Stock Module for Hipanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-stock
 * @package   hipanel-module-stock
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2016, HiQDev (http://hiqdev.com/)
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
     * @var string the type of tariff
     * @see getFilter()
     */
    public $tariffType;

    /** {@inheritdoc} */
    public function getFilter()
    {
        return ArrayHelper::merge(parent::getFilter(), [
            'type_in' => ['format' => $this->tariffType],
        ]);
    }
}
