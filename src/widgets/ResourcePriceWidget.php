<?php

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
            NumberFormatter::MAX_FRACTION_DIGITS => 4
        ]);
    }
}
