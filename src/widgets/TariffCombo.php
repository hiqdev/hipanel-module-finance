<?php

/*
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2016, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\widgets;

use hipanel\helpers\ArrayHelper;
use hiqdev\combo\Combo;

/**
 * Class TariffCombo.
 */
class TariffCombo extends Combo
{
    /** {@inheritdoc} */
    public $type = 'finance/tariff';

    /** {@inheritdoc} */
    public $name = 'tariff';

    /** {@inheritdoc} */
    public $url = '/finance/tariff/search';

    /** {@inheritdoc} */
    public $_return = ['id'];

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
