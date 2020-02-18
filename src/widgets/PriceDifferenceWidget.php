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
use Money\MoneyFormatter;
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
    /**
     * @var MoneyFormatter
     */
    private $formatter;

    public function __construct(MoneyFormatter $formatter, $config = [])
    {
        parent::__construct($config);
        $this->formatter = $formatter;
    }

    public function run()
    {
        if (!$this->areCurrenciesSame()) {
            $widget = ResourcePriceWidget::widget([
                'price' => $this->old,
            ]);
            return Html::tag('span', $widget, ['class' => 'text-gray']);
        }
        return $this->renderDifferenceWidget();
    }

    private function renderDifferenceWidget()
    {
        $diff = $this->new->subtract($this->old);
        $diffAmount = $diff->getAmount();
        if ($diffAmount === 0) {
            return '';
        }

        return Html::tag(
            'span',
            ($diffAmount > 0 ? '+' : '') . $this->formatter->format($diff),
            ['class' => $diffAmount > 0 ? 'text-success' : 'text-danger']
        );
    }

    private function areCurrenciesSame(): bool
    {
        return $this->old->getCurrency()->equals($this->new->getCurrency());
    }
}
