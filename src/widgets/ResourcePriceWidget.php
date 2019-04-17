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

use NumberFormatter;
use Yii;
use yii\base\Widget;

class ResourcePriceWidget extends Widget
{
    public $price;
    public $currency;

    public function run()
    {
        return Yii::$app->formatter->asCurrency($this->price, $this->currency, [
            NumberFormatter::MAX_FRACTION_DIGITS => 4,
        ]);
    }
}
