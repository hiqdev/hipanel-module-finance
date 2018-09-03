<?php

namespace hipanel\modules\finance\grid;

use hipanel\grid\CurrencyColumn;
use hipanel\modules\finance\logic\bill\MonthlyQuantity;
use hipanel\modules\finance\logic\bill\QuantityFormatterFactoryInterface;
use hipanel\modules\finance\models\Charge;
use hipanel\modules\finance\widgets\LinkToObjectResolver;
use hipanel\modules\finance\widgets\PriceType;
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
                'format' => 'raw',
                'value' => function (Charge $model) {
                    return PriceType::widget([
                        'model' => $model,
                        'field' => 'ftype'
                    ]);
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
                    return LinkToObjectResolver::widget([
                        'model' => $model,
                        'idAttribute' => 'object_id',
                        'typeAttribute' => 'class',
                        'labelAttribute' => 'name',
                    ]) . ($model->label ? " &ndash; $model->label" : '');
                },
            ],
            'quantity' => [
                'attribute' => 'quantity',
                'format' => 'raw',
                'value' => function (Charge $model) {
                    return $this->renderQuantity($model);
                }
            ],
            'time' => [
                'format' => 'raw',
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
