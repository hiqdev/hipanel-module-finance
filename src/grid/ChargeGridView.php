<?php

namespace hipanel\modules\finance\grid;

use hipanel\grid\CurrencyColumn;
use hipanel\modules\finance\logic\bill\QuantityFormatterFactoryInterface;
use hipanel\modules\finance\models\Charge;
use hipanel\widgets\ArraySpoiler;
use hiqdev\php\units\Quantity;
use hiqdev\php\units\yii2\formatters\IntlFormatter;
use Yii;
use yii\helpers\Html;

/**
 * Class ChargeGridView
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class ChargeGridView extends \hipanel\grid\BoxedGridView
{
    /**
     * @var QuantityFormatterFactoryInterface
     */
    private $formatterFactory;

    /**
     * ChargeGridView constructor.
     *
     * @param QuantityFormatterFactoryInterface $formatterFactory
     * @param array $config
     */
    public function __construct(QuantityFormatterFactoryInterface $formatterFactory, array $config = [])
    {
        parent::__construct($config);
        $this->formatterFactory = $formatterFactory;
    }

    public function columns()
    {
        return array_merge(parent::columns(), [
            'type_label' => [
                'label' => Yii::t('hipanel', 'Type'),
                'format' => 'html',
                'value' => function ($model) {
                    static $colors = [
                        'correction' => 'normal',
                        'exchange' => 'warning',
                        'deposit' => 'success',
                    ];
                    $color = $colors[$model->type] ?: 'muted';

                    return Html::tag('b', Yii::t('hipanel:finance', $model->type_label), ['class' => "text-$color"]);
                },
            ],
            'sum' => [
                'class' => CurrencyColumn::class,
                'attribute' => 'sum',
                'colors' => ['danger' => 'warning'],
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => function ($model) {
                    return ['class' => 'text-right' . ($model->sum > 0 ? ' text-bold' : '')];
                },
            ],
            'label' => [
                'attribute' => 'label',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->name) {
                        $descr = Yii::t('hipanel', ucfirst($model->class)) . ' ' . Html::tag('b', $model->name);
                    } elseif ($model->label) {
                        $descr = $model->label;
                    }
                    $text = mb_strlen($descr) > 70 ? ArraySpoiler::widget(['data' => $descr]) : $descr;

                    return $text;
                },
            ],
            'quantity' => [
                'attribute' => 'quantity',
                'format' => 'html',
                'value' => function (Charge $model) {
                    return $this->renderQuantity($model);
                }
            ],
            'time' => [
                'format' => 'html',
                'filter' => false,
                'enableSorting' => false,
                'contentOptions' => ['class' => 'text-nowrap'],
                'value' => function ($model) {
                    list($date, $time) = explode(' ', $model->time, 2);

                    if (\in_array($model->type, [
                        'discount', 'domain', 'monthly', 'overuse', 'premium_package',
                        'feature', 'intercept', 'periodic',
                    ], true)) {
                        return Yii::$app->formatter->asDate($date, 'LLLL y');
                    }

                    return $time === '00:00:00'
                        ? Yii::$app->formatter->asDate($date)
                        : Yii::$app->formatter->asDateTime($model->time);
                },
            ],
        ]);
    }

    /**
     * @return string
     */
    private function renderQuantity(Charge $charge): string
    {
        $formatter = $this->formatterFactory->forCharge($charge);

        if ($formatter !== null) {
            return Html::tag('nobr', Html::tag('b', $formatter->format()));
        }

        return '';
    }
}
