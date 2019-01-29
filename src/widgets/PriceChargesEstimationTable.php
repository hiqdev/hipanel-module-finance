<?php

namespace hipanel\modules\finance\widgets;

use hipanel\models\Ref;
use yii\base\Widget;

/**
 * Class PriceChargesEstimationTable
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
