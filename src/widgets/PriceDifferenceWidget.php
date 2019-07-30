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

use hiqdev\hiart\Exception;
use Money\Money;
use Yii;
use yii\base\Widget;
use yii\helpers\Html;

class PriceDifferenceWidget extends Widget
{
    /**
     * @var Money
     */
    public $old;

    /**
     * @var Money
     */
    public $new;

    public function run()
    {
        if (!$this->checkCurrencies()) {
            echo ResourcePriceWidget::widget([
                'price' => $this->old,
            ]);
        }
        echo $this->renderDifferenceWidget();
    }

    private function renderDifferenceWidget()
    {
        $widget = '';
        $diff = floatval($this->new->getAmount() - $this->old->getAmount());
        if ($diff !== (float) 0) {
            $widget = Html::tag(
                'span',
                ($diff > 0 ? '+' : '') . Yii::$app->formatter->asDecimal($diff, 2),
                ['class' => $diff > 0 ? 'text-success' : 'text-danger']
            );
        }
        return $widget;
    }

    private function checkCurrencies()
    {
        try {
            $res = $this->old->getCurrency() === $this->new->getCurrency();
        } catch (Exception $e) {
            $res = 1;
        }
        return $res;
//        return $this->old->getCurrency() === $this->new->getCurrency();
    }
}
