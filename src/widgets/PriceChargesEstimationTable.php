<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\widgets;

use yii\base\Widget;

/**
 * Class PriceChargesEstimationTable.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class PriceChargesEstimationTable extends Widget
{
    /**
     * @var array
     */
    public $charges;

    public function run()
    {
        return $this->render('priceChargesEstimationTable', ['charges' => $this->charges]);
    }
}
