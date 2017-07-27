<?php

namespace hipanel\modules\finance\widgets;

use yii\base\Widget;
use yii\helpers\Html;

class AvailableMerchants extends Widget
{
    public $merchants = [];

    public function run()
    {
        $list = array_map(function ($merchant) { return Html::tag('b', $merchant->label); }, $this->merchants);

        return implode(',&nbsp; ', array_unique($list));
    }
}
