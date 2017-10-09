<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\grid;

use hipanel\grid\CurrencyColumn;
use hipanel\grid\MainColumn;
use hipanel\helpers\StringHelper;
use hipanel\helpers\Url;
use hipanel\modules\finance\logic\bill\BillQuantityFactory;
use hipanel\modules\finance\logic\bill\BillQuantityInterface;
use hipanel\modules\finance\menus\BillActionsMenu;
use hipanel\modules\finance\models\Bill;
use hipanel\modules\finance\widgets\BillTypeFilter;
use hipanel\modules\finance\widgets\ColoredBalance;
use hipanel\widgets\ArraySpoiler;
use hiqdev\combo\StaticCombo;
use hiqdev\yii2\menus\grid\MenuColumn;
use Yii;
use yii\helpers\Html;

class BillGridView extends \hipanel\grid\BoxedGridView
{
    public $currencies = [];

    public function columns()
    {
        return array_merge(parent::columns(), [
            'bill' => [
                'class' => MainColumn::class,
                'attribute' => 'bill',
                'filterAttribute' => 'bill_like',
            ],
            'time' => [
                'format' => 'html',
                'filter' => false,
                'contentOptions' => ['class' => 'text-nowrap'],
                'value' => function ($model) {
                    list($date, $time) = explode(' ', $model->time, 2);

                    if (in_array($model->gtype, [
                        'discount', 'domain', 'monthly', 'overuse', 'premium_package',
                        'feature', 'intercept', 'periodic',
                    ], true)) {
                        return Yii::$app->formatter->asDate($date, 'LLLL y');
                    }

                    return $time === '00:00:00' ? Yii::$app->formatter->asDate($date) : Yii::$app->formatter->asDateTime($model->time);
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
            'sum_editable' => [
                'class' => CurrencyColumn::class,
                'attribute' => 'sum',
                'colors' => ['danger' => 'warning'],
                'headerOptions' => ['class' => 'text-right'],
                'urlCallback' => function ($model, $key) {
                    return Yii::$app->user->can('bill.update') ? Url::to(['bill/update', 'id' => $model->id]) : null;
                },
                'contentOptions' => function ($model) {
                    return ['class' => 'text-right' . ($model->sum > 0 ? ' text-bold' : '')];
                },
            ],
            'quantity' => [
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right text-bold'],
            ],
            'balance' => [
                'attribute' => 'balance',
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => function ($model, $key, $index) {
                    return ['class' => 'text-right' . ($index ? '' : ' text-bold')];
                },
                'value' => function ($model) {
                    return ColoredBalance::widget(compact('model'));
                },
                'filterAttribute' => 'currency_in',
                'filterOptions' => ['class' => 'narrow-filter'],
                'filter' => function ($column, $filterModel) {
                    $currencies = array_combine(array_keys($this->currencies), array_map(function ($k) {
                        return StringHelper::getCurrencySymbol($k);
                    }, array_keys($this->currencies)));

                    return Html::activeDropDownList($filterModel, 'currency_in', $currencies, ['class' => 'form-control', 'prompt' => '--']);
                }
            ],
            'gtype' => [
                'attribute' => 'gtype',
            ],
            'type_label' => [
                'filter' => function ($column, $filterModel) {
                    return BillTypeFilter::widget([
                        'options' => ['class' => 'form-control text-right'],
                        'attribute' => 'ftype',
                        'model' => $filterModel,
                    ]);
                },
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => function ($model) {
                    return ['class' => 'text-right'];
                },
                'value' => function ($model) {
                    static $colors = [
                        'correction' => 'normal',
                        'exchange' => 'warning',
                        'deposit' => 'success',
                    ];
                    $color = $colors[$model->gtype] ?: 'muted';

                    return Html::tag('b', Yii::t('hipanel:finance', $model->type_label), ['class' => "text-$color"]);
                },
            ],
            'description' => [
                'attribute' => 'descr',
                'format' => 'raw',
                'value' => function ($model) {
                    $descr = $model->descr ?: $model->label;
                    $text = mb_strlen($descr) > 70 ? ArraySpoiler::widget(['data' => $descr]) : $descr;
                    $tariff = $model->tariff ? Html::tag('span',
                        Yii::t('hipanel', 'Tariff') . ': ' . Html::a($model->tariff,
                            ['@tariff/view', 'id' => $model->tariff_id]), ['class' => 'pull-right']) : '';
                    $amount = self::billQuantity($model);
                    $object = $this->objectTag($model);

                    return $tariff . $amount . ' ' . implode('<br>', array_filter([$object, $text]));
                },
            ],
            'tariff_link' => [
                'attribute' => 'tariff',
                'format' => 'html',
                'value' => function ($model) {
                    return $this->tariffLink($model);
                },
            ],
            'object' => [
                'attribute' => 'object',
                'format' => 'html',
                'value' => function ($model) {
                    return $this->objectTag($model);
                },
            ],
            'actions' => [
                'class' => MenuColumn::class,
                'menuClass' => BillActionsMenu::class,
            ],
        ]);
    }

    public function tariffLink($model)
    {
        return Html::a($model->tariff, ['@tariff/view', 'id' => $model->tariff_id]);
    }

    public function objectTag($model)
    {
        return $model->object ? implode(':&nbsp;', [$model->class_label, $this->objectLink($model)]) : '';
    }

    /**
     * Creates link to object details page.
     * @param Bill $model
     */
    public function objectLink($model)
    {
        return $model->class === 'device'
            ? Html::a($model->object, ['@server/view', 'id' => $model->object_id])
            : Html::tag('b', $model->object);
    }

    /**
     * @param Bill $model
     * @return null|string
     */
    public static function billQuantity($model)
    {
        $factory = Yii::$container->get(BillQuantityFactory::class);
        $billQty = $factory->createByType($model->type, $model);

        if ($billQty and $billQty instanceof BillQuantityInterface) {
            return Html::tag('nobr', Html::tag('b', $billQty->getText()));
        }

        return null;
    }
}
