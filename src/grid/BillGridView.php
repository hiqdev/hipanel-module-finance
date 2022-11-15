<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\grid;

use hipanel\grid\CurrencyColumn;
use hipanel\grid\MainColumn;
use hipanel\grid\RefColumn;
use hipanel\helpers\Url;
use hipanel\modules\client\grid\ClientColumn;
use hipanel\modules\finance\helpers\CurrencyFilter;
use hipanel\modules\finance\logic\bill\QuantityFormatterFactoryInterface;
use hipanel\modules\finance\menus\BillActionsMenu;
use hipanel\modules\finance\models\Bill;
use hipanel\modules\finance\models\Charge;
use hipanel\modules\finance\widgets\BillIsPayedDropdown;
use hipanel\modules\finance\widgets\BillType;
use hipanel\modules\finance\widgets\BillTypeFilter;
use hipanel\modules\finance\widgets\ColoredBalance;
use hipanel\modules\finance\widgets\LinkToObjectResolver;
use hipanel\widgets\ArraySpoiler;
use hipanel\widgets\ClientSellerLink;
use hipanel\widgets\IconStateLabel;
use hiqdev\yii2\menus\grid\MenuColumn;
use Yii;
use yii\helpers\Html;

/**
 * Class BillGridView.
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
            'requisite' => [
                'format' => 'raw',
                'attribute' => 'requisite',
                'value' => fn($model) => Html::a(Html::encode($model->requisite), ['@requisite/view', 'id' => $model->requisite_id]),
                'visible' => Yii::$app->user->can('requisites.read'),
            ],
            'payment_status' => [
                'format' => 'raw',
                'attribute' => 'is_payed',
                'label' => Yii::t('hipanel:finance', 'Payment status'),
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
                'value' => static function (Bill $bill): string {
                    if ($bill->is_payed) {
                        return Html::tag('span', Yii::t('hipanel:finance', 'Bill is paid'), ['class' => 'label label-success']);
                    }

                    return Html::tag('span', Yii::t('hipanel:finance', 'Bill not paid'), ['class' => 'label label-danger']);
                },
            ],
            'is_payed' => [
                'format' => 'raw',
                'attribute' => 'is_payed',
                'label' => Yii::t('hipanel:finance', 'Is paid?'),
                'headerOptions' => ['class' => 'narrow-filter text-center'],
                'contentOptions' => ['class' => 'text-center'],
                'value' => fn(Bill $bill): string => IconStateLabel::widget([
                    'model' => $bill,
                    'attribute' => 'is_payed',
                    'icons' => ['fa-check-circle', 'fa-times-circle'],
                    'colors' => ['#00a65a', '#d73925'],
                    'messages' => [
                        Yii::t('hipanel:finance', 'Bill is paid'),
                        Yii::t('hipanel:finance', 'Bill is not paid'),
                    ],
                ]),
            ],
            'bill' => [
                'class' => MainColumn::class,
                'attribute' => 'bill',
                'filterAttribute' => 'bill_like',
            ],
            'client_id' => [
                'class' => ClientColumn::class,
                'exportedColumns' => ['export_bill_id', 'client_id'],
            ],
            'export_bill_id' => [
                'label' => Yii::t('hipanel', 'Bill ID'),
                'value' => static fn($bill): string => $bill->id ?? '',
            ],
            'time' => [
                'format' => 'raw',
                'filter' => false,
                'label' => Yii::t('hipanel', 'Time'),
                'sortAttribute' => 'time',
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
                'exportedColumns' => ['export_year', 'export_date', 'export_time', 'export_tariff', 'export_tariff_type'],
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
                'filterAttribute' => 'currency_in',
                'filterOptions' => ['class' => 'narrow-filter text-right'],
                'filter' => function ($column, $filterModel) {
                    $currencies = CurrencyFilter::addSymbolAndFilter($this->currencies);
                    return Html::activeDropDownList($filterModel, 'currency_in', $currencies, ['class' => 'form-control', 'prompt' => '--']);
                },
                'exportedColumns' => ['export_sum', 'export_currency'],
            ],
            'export_sum' => [
                'format' => 'decimal',
                'label' => Yii::t('hipanel:finance', 'Sum'),
                'value' => static fn($bill): string => $bill->sum,
            ],
            'export_currency' => [
                'label' => Yii::t('hipanel', 'Currency'),
                'value' => static fn($bill): string => $bill->currency,
            ],
            'export_balance' => [
                'format' => 'decimal',
                'label' => Yii::t('hipanel', 'Balance'),
                'value' => static fn($bill): string => $bill->balance,
            ],
            'export_tariff' => [
                'label' => Yii::t('hipanel:finance', 'Tariff'),
                'value' => static fn($bill): string => $bill->tariff ?? '',
            ],
            'export_tariff_type' => [
                'label' => Yii::t('hipanel:finance', 'Tariff type'),
                'value' => static fn($bill): string => $bill->tariff_type ?? '',
            ],
            'export_year' => [
                'label' => Yii::t('hipanel', 'Year'),
                'format' => ['date', 'php:Y'],
                'value' => static fn($bill): string => $bill->time,
            ],
            'export_date' => [
                'label' => Yii::t('hipanel', 'Date'),
                'format' => ['date', 'php:Y-m-d'],
                'value' => static fn($bill): string => $bill->time,
            ],
            'export_time' => [
                'label' => Yii::t('hipanel', 'Time'),
                'format' => ['date', 'php:H:i:s'],
                'value' => static fn($bill): string => $bill->time,
            ],
            'index_page_balance' => [
                'attribute' => 'balance',
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => function ($model, $key, $index) {
                    return ['class' => 'text-right' . ($index ? '' : ' text-bold')];
                },
                'value' => function (Bill $model): string {
                    $isPayed = IconStateLabel::widget([
                        'model' => $model,
                        'attribute' => 'is_payed',
                        'icons' => ['fa-check-circle', 'fa-times-circle'],
                        'colors' => ['#00a65a94', '#bdbdbd'],
                        'cssStyles' => ['padding-top' => '4px'],
                        'messages' => [
                            Yii::t('hipanel:finance', 'Bill is paid'),
                            Yii::t('hipanel:finance', 'Bill is not paid'),
                        ],
                    ]);
                    $balance = ColoredBalance::widget(compact('model'));

                    return Html::tag('span', implode('', [$isPayed, $balance]), ['style' => [
                        'display' => 'flex',
                        'justify-content' => 'space-between',
                        'align-items' => 'center',
                    ]]);
                },
                'filterOptions' => ['class' => 'narrow-filter text-right'],
                'filter' => fn($column, $filterModel): string => BillIsPayedDropdown::widget(['model' => $filterModel]),
                'exportedColumns' => ['export_balance', 'is_payed'],
            ],
            'quantity' => [
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right text-bold'],
                'value' => function (Bill $bill) {
                    return $this->formatQuantity($bill);
                },
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
                    $currencies = CurrencyFilter::addSymbolAndFilter($this->currencies);
                    return Html::activeDropDownList($filterModel, 'currency_in', $currencies, ['class' => 'form-control', 'prompt' => '--']);
                },
            ],
            'gtype' => [
                'attribute' => 'gtype',
            ],
            'type_label' => [
                'filterOptions' => ['class' => 'text-right'],
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
                'exportedColumns' => ['export_type_type', 'export_payment_type'],
            ],
            'export_type_type' => [
                'label' => Yii::t('hipanel', 'Type'),
                'value' => static fn(Bill $bill): string => Yii::t('hipanel.finance.billTypes', $bill->type_label),
            ],
            'export_payment_type' => [
                'label' => Yii::t('hipanel', 'Payment Type'),
                'value' => static fn(Bill $bill): string => Yii::t('hipanel.finance.billTypes', explode(',', $bill->ftype)[0]),
            ],
            'description' => [
                'class' => RefColumn::class,
                'attribute' => 'descr',
                'format' => 'raw',
                'i18nDictionary' => 'hipanel:finance',
                'value' => function (Bill $model) {
                    $requisite = $model->requisite ? Html::tag('small', Html::encode($model->requisite), ['class' => 'label bg-purple']) : null;
                    $descr = Html::encode($model->descr ?? $model->label ?? '');
                    $text = mb_strlen($descr) > 70 ? ArraySpoiler::widget(['data' => $descr]) : $descr;
                    $tariff = $model->tariff ? Html::tag('span',
                        Yii::t('hipanel', 'Tariff') . ': ' . $this->tariffLink($model), ['class' => 'pull-right']) : '';
                    $amount = $this->formatQuantity($model);
                    $object = $this->objectTag($model);

                    return $tariff . $amount . ' ' . implode('<br>', array_filter([$requisite, $object, nl2br($text)]));
                },
                'exportedValue' => function (Bill $model): string {
                    $text = $model->descr ?: $model->label;
                    $tariff = $model->tariff ?
                        Html::tag('span', Yii::t('hipanel', 'Tariff') . ': ' . $this->tariffLink($model), ['class' => 'pull-right']) : '';
                    $amount = $this->formatQuantity($model);
                    $object = $this->objectTag($model);

                    return $tariff . ' ' . $amount . ' ' . implode('<br>', array_filter([$object, $text]));
                },
            ],
            'tariff_link' => [
                'attribute' => 'tariff',
                'format' => 'raw',
                'value' => function (Bill $model) {
                    return $this->tariffLink($model);
                },
            ],
            'object' => [
                'attribute' => 'object',
                'format' => 'raw',
                'value' => function (Bill $model) {
                    return $this->objectTag($model);
                },
            ],
            'actions' => [
                'class' => MenuColumn::class,
                'menuClass' => BillActionsMenu::class,
            ],
            'common_object_link' => [
                'format' => 'raw',
                'value' => function (Charge $model) {
                    $link = LinkToObjectResolver::widget([
                        'model'          => $model->commonObject,
                        'labelAttribute' => 'name',
                        'idAttribute'    => 'id',
                        'typeAttribute'  => 'type',
                        'customLinks' => [
                            'part' => '@server/view',
                        ],
                    ]);

                    return $link;
                },
            ],
        ]);
    }

    public function tariffLink(Bill $model): ?string
    {
        $canSeeLink = Yii::$app->user->can('plan.read');
        $tariff = Html::encode($model->tariff);

        return $canSeeLink ? Html::a($tariff, ['@plan/view', 'id' => $model->tariff_id]) : $tariff;
    }

    public function objectTag($model): string
    {
        return $model->object ? implode(':&nbsp;', [Yii::t('hipanel', Html::encode($model->class_label)), $this->objectLink($model)]) : '';
    }

    /**
     * Creates link to object details page.
     *
     * @param Bill $model
     * @return string
     */
    public function objectLink(Bill $model): string
    {
        return $model->class === 'device' && Yii::getAlias('@server/view', false)
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
