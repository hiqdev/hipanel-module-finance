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

use Yii;
use yii\base\Widget;
use yii\helpers\Html;

class PriceDifferenceWidget extends Widget
{
    /**
     * @var float
     */
    public $old;

    /**
     * @var float
     */
    public $new;

    public function run()
    {
        $diff = floatval($this->new - $this->old);
        if ($diff !== (float) 0) {
            echo Html::tag(
                'span',
                ($diff > 0 ? '+' : '') . Yii::$app->formatter->asDecimal($diff, 2),
                ['class' => $diff > 0 ? 'text-success' : 'text-danger']
            );
        }

        return;
    }
}
