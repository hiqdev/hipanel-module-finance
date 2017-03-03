<?php

namespace hipanel\modules\finance\widgets;

use hipanel\modules\finance\models\ExchangeRate;
use hipanel\widgets\ArraySpoiler;
use Yii;
use yii\base\Widget;
use yii\helpers\Html;

class ExchangeRatesLine extends Widget
{
    /**
     * @var ExchangeRate[]
     */
    public $rates;

    public function run()
    {
        if (!Yii::$app->user->can('manage')) {
            return '';
        }

        return $this->renderLine();
    }

    protected function renderLine()
    {
        return Html::tag('span', ArraySpoiler::widget([
            'data' => $this->rates,
            'visibleCount' => 3,
            'button' => [
                'label' => '+{count}',
                'popoverOptions' => ['html' => true],
            ],
            'formatter' => function ($model) {
                /** @var \hipanel\modules\finance\models\ExchangeRate $model */
                return Html::tag('span', $model->from . '/' . $model->to, ['style' => 'font-weight: 400']) . ': ' . $model->rate;
            }
        ]), ['style' => 'padding-left: 20px; color: #737272;']);
    }
}
