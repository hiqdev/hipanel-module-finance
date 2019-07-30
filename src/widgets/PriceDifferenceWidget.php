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
    /** @var float */
    public $new;

    /** @var float */
    public $old;

    /** @var string|null */
    public $newCurrency;

    /** @var string|null */
    public $oldCurrency;

    public function run()
    {
        if (!$this->oldCurrency || !$this->newCurrency || $this->checkCurrencies()) {
            return $this->renderDifferenceWidget();
        }
        return ResourcePriceWidget::widget([
            'price' => $this->old,
            'currency' => $this->oldCurrency,
        ]);
    }

    private function renderDifferenceWidget(): string
    {
        $diff = floatval($this->new - $this->old);
        if ($diff === (float) 0) {
            return '';
        }
        return Html::tag(
            'span',
            ($diff > 0 ? '+' : '') . Yii::$app->formatter->asDecimal($diff, 2),
            ['class' => $diff > 0 ? 'text-success' : 'text-danger']
        );
    }

    private function checkCurrencies(): bool
    {
        return $this->oldCurrency === $this->newCurrency;
    }
}
