<?php

namespace hipanel\modules\finance\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;

class PriceDifferenceWidget extends Widget
{
    public $old;

    public $new;

    public function run()
    {
        $diff = $this->new - $this->old;
        if ($diff != 0) {
            print Html::tag(
                'span',
                ($diff > 0 ? '+' : '') . Yii::$app->formatter->asDecimal($diff, 2),
                ['class' => $diff > 0 ? 'text-success' : 'text-danger']
            );
        }

        return;
    }
}
