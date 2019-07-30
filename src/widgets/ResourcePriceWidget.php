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

use Money\Money;
use NumberFormatter;
use Yii;
use yii\base\Widget;

class ResourcePriceWidget extends Widget
{
    /**
     * @var Money
     */
    public $price;

    public function run()
    {
        $price = $this->price;
        return Yii::$app->formatter->asCurrency($price->getAmount(), $price->getCurrency(), [
            NumberFormatter::MAX_FRACTION_DIGITS => 4,
        ]);
    }
}
