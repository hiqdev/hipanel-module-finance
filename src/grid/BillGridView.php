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
use hipanel\modules\finance\logic\bill\QuantityFormatterFactoryInterface;
use hipanel\modules\finance\menus\BillActionsMenu;
use hipanel\modules\finance\models\Bill;
use hipanel\modules\finance\models\Charge;
use hipanel\modules\finance\widgets\BillType;
use hipanel\modules\finance\widgets\BillTypeFilter;
use hipanel\modules\finance\widgets\ColoredBalance;
use hipanel\modules\finance\widgets\LinkToObjectResolver;
use hipanel\widgets\ArraySpoiler;
use hiqdev\yii2\menus\grid\MenuColumn;
use Yii;
use yii\helpers\Html;

/**
 * Class BillGridView
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class BillGridView extends \hipanel\grid\BoxedGridView
{
    /**
     * @var QuantityFormatterFactoryInterface
     */
    private $quantityFactory;

    public function __construct(QuantityFormatterFactoryInterface $quantityFactory, array $config = [])
    {
        parent::__construct($config);

        $this->quantityFactory = $quantityFactory;
    }

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
                'value' => function (Bill $model) {
                    list($date, $time) = explode(' ', $model->time, 2);

                    return in_array($model->gtype, [
                        'discount',
                        'domain',
                        'monthly',
                        'overuse',
                        'premium_package',
                        'feature',
                        'intercept',
                        'periodic',
                    ], true) && $time === '00:00:00'
                        ? Yii::$app->formatter->asDate($date, 'LLLL y')
                        : Yii::$app->formatter->asDateTime($model->time);
                },
            ],
            'sum' => [
                'class' => CurrencyColumn::class,
                'attribute' => 'sum',
                'colors' => ['danger' => 'warning'],
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => function (Bill $model) {
                    return ['class' => 'text-right' . ($model->sum > 0 ? ' text-bold' : '')];
                },
            ],
            'sum_editable' => [
                'class' => CurrencyColumn::class,
                'format' => 'raw',
                'attribute' => 'sum',
                'colors' => ['danger' => 'warning'],
                'headerOptions' => ['class' => 'text-right'],
                'urlCallback' => function ($model, $key) {
                    return Yii::$app->user->can('bill.read')
                        ? Url::to(['@bill/view', 'id' => $model->id])
                        : null;
                },
                'contentOptions' => function (Bill $model) {
                    return ['class' => 'text-right' . ($model->sum > 0 ? ' text-bold' : '')];
                },
            ],
            'quantity' => [
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right text-bold'],
                'value' => function (Bill $bill) {
                    return $this->formatQuantity($bill);
                }
            ],
            'balance' => [
                'attribute' => 'balance',
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => function ($model, $key, $index) {
                    return ['class' => 'text-right' . ($index ? '' : ' text-bold')];
                },
                'value' => function (Bill $model) {
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
                        'options' => ['class' => 'form-control text-right', 'style' => 'max-width: 12em'],
                        'attribute' => 'ftype',
                        'model' => $filterModel,
                    ]);
                },
                'sortAttribute' => 'type',
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => function (Bill $model) {
                    return ['class' => 'text-right'];
                },
                'value' => function (Bill $model) {
                    return BillType::widget([
                        'model' => $model,
                        'field' => 'ftype',
                        'labelField' => 'type_label',
                    ]);
                },
            ],
            'description' => [
                'attribute' => 'descr',
                'format' => 'raw',
                'value' => function (Bill $model) {
                    $descr = $model->descr ?: $model->label;
                    $text = mb_strlen($descr) > 70 ? ArraySpoiler::widget(['data' => $descr]) : $descr;
                    $tariff = $model->tariff ? Html::tag('span',
                        Yii::t('hipanel', 'Tariff') . ': ' . Html::a($model->tariff,
                            ['@plan/view', 'id' => $model->tariff_id]), ['class' => 'pull-right']) : '';
                    $amount = $this->formatQuantity($model);
                    $object = $this->objectTag($model);

                    return $tariff . $amount . ' ' . implode('<br>', array_filter([$object, $text]));
                },
            ],
            'tariff_link' => [
                'attribute' => 'tariff',
                'format' => 'html',
                'value' => function (Bill $model) {
                    return $this->tariffLink($model);
                },
            ],
            'object' => [
                'attribute' => 'object',
                'format' => 'html',
                'value' => function (Bill $model) {
                    return $this->objectTag($model);
                },
            ],
            'actions' => [
                'class' => MenuColumn::class,
                'menuClass' => BillActionsMenu::class,
            ],
            'common_object_link' => [
                'format' => 'html',
                'value' => function (Charge $model) {
                    $link = LinkToObjectResolver::widget([
                        'model'          => $model->commonObject,
                        'labelAttribute' => 'name',
                        'idAttribute'    => 'id',
                        'typeAttribute'  => 'type',
                        'customLinks' => [
                            'part' => '@server/view'
                        ]
                    ]);
                    return $link;
                }
            ],
        ]);
    }

    public function tariffLink($model): string
    {
        return Html::a($model->tariff, ['@plan/view', 'id' => $model->tariff_id]);
    }

    public function objectTag($model): string
    {
        return $model->object ? implode(':&nbsp;', [$model->class_label, $this->objectLink($model)]) : '';
    }

    /**
     * Creates link to object details page.
     *
     * @param Bill $model
     * @return string
     */
    public function objectLink(Bill $model): string
    {
        return $model->class === 'device'
            ? Html::a($model->object, ['@server/view', 'id' => $model->object_id])
            : Html::tag('b', $model->object);
    }

    private function formatQuantity(Bill $bill): string
    {
        $billQty = $this->quantityFactory->forBill($bill);

        if ($billQty !== null) {
            return Html::tag('nobr', Html::tag('b', $billQty->format()));
        }

        return '';
    }
}
