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

use hipanel\modules\finance\models\ExchangeRate;
use hipanel\widgets\ArraySpoiler;
use Tuck\Sort\Sort;
use Yii;
use yii\base\Widget;
use yii\helpers\Html;

class ExchangeRatesLine extends Widget
{
    /**
     * @var ExchangeRate[]
     */
    public $rates;

    /**
     * @var string[] pairs that should be shown first (if exist)
     */
    public $priorityPairCodes = [
        'USD/EUR',
        'EUR/USD',
        'USB/BTC',
    ];

    public function run()
    {
        if (!Yii::$app->user->can('manage') || empty($this->rates)) {
            return '';
        }

        return $this->renderLine();
    }

    /**
     * @param ExchangeRate[] $rates not sorted rates array
     * @return ExchangeRate[] sorted rates
     */
    private function sortRates(array $rates): array
    {
        $chain = Sort::chain()
            ->asc(function (ExchangeRate $rate) {
                $pos = array_search($rate->pairCode(), $this->priorityPairCodes, true);

                return $pos !== false ? $pos : INF;
            })
            ->compare(function (ExchangeRate $a, ExchangeRate $b) {
                return strnatcasecmp($a->pairCode(), $b->pairCode());
            });

        return $chain->values($rates);
    }

    protected function renderLine()
    {
        return Html::tag('span', ArraySpoiler::widget([
            'data' => $this->sortRates($this->rates),
            'visibleCount' => 3,
            'button' => [
                'label' => '+{count}',
                'popoverOptions' => ['html' => true],
            ],
            'hiddenDelimiter' => '<br />',
            'formatter' => function (ExchangeRate $model) {
                return Html::tag('span', $model->pairCode(), ['style' => 'font-weight: 400']) . ': ' . $model->rate;
            },
        ]), ['style' => 'padding-left: 20px; color: #737272;']);
    }
}
