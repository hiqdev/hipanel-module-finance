<?php

namespace hipanel\modules\finance\widgets;

use hipanel\widgets\ArraySpoiler;
use yii\base\Widget;
use yii\helpers\Html;

class AvailableMerchants extends Widget
{
    public $merchants = [];

    public $arraySpoilerOptions = [];

    public function run()
    {
        $options = empty($this->arraySpoilerOptions) ? $this->defaultOptions() : $this->arraySpoilerOptions;

        return ArraySpoiler::widget(array_merge($options, ['data' => $this->merchants]));
    }

    private function defaultOptions()
    {
        return [
            'visibleCount' => count($this->merchants),
            'formatter' => function ($merchant) {
                return Html::tag('b', $merchant->label);
            },
            'delimiter' => ',&nbsp; ',
        ];
    }
}
