<?php declare(strict_types=1);
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\grid;

use hipanel\grid\BoxedGridView;
use hipanel\grid\CurrencyColumn;
use hipanel\helpers\ArrayHelper;
use hipanel\helpers\Url;
use hipanel\modules\client\grid\ClientColumn;
use hipanel\modules\finance\logic\bill\QuantityFormatterFactoryInterface;
use hipanel\modules\finance\models\Charge;
use hipanel\modules\finance\models\ChargeSearch;
use hipanel\modules\finance\widgets\BillType;
use hipanel\modules\finance\widgets\BillTypeFilter;
use hipanel\modules\finance\widgets\LinkToObjectResolver;
use hipanel\widgets\IconStateLabel;
use hipanel\widgets\TagsManager;
use hiqdev\combo\StaticCombo;
use hiqdev\higrid\DataColumn;
use ReflectionClass;
use Yii;
use yii\helpers\Html;

/**
 * Class ChargeGridView.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class ChargeGridView extends BoxedGridView
{
    use AmountColumns;

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
        return ArrayHelper::merge(parent::columns(), [
            'client_tags' => [
                'label' => Yii::t('hipanel:finance', 'Client tags'),
                'value' => fn(Charge $charge) => $charge->customer?->tags ? implode(', ', $charge->customer->tags) : '',
            ],
            'label' => [
                'attribute' => 'label_ilike',
                'sortAttribute' => 'label_ilike',
                'label' => Yii::t('hipanel', 'Description'),
                'value' => fn(Charge $model): string => $model->label ?? '',
            ],
            'tariff' => [
                'attribute' => 'tariff_id',
                'label' => Yii::t('hipanel', 'Plan'),
                'filter' => false,
                'format' => 'raw',
                'value' => fn(Charge $model): string => $this->tariffLink($model),
            ],
            'type_label' => [
                'label' => Yii::t('hipanel', 'Type'),
                'headerOptions' => ['class' => 'text-right', 'style' => 'max-width: 25em;'],
                'contentOptions' => ['style' => 'white-space: nowrap;'],
                'format' => 'raw',
                'value' => fn(Charge $model) => BillType::widget([
                    'model' => $model,
                    'field' => 'ftype',
                    'labelField' => 'type_label',
                ]),
                'filterAttribute' => 'type',
                'filterInputOptions' => ['style' => 'min-width: 10%;'],
                'filter' => fn(DataColumn $column, ChargeSearch $filterModel): string => BillTypeFilter::widget([
                    'attribute' => 'type_id',
                    'model' => $filterModel,
                ]),
                'exportedColumns' => ['export_type', 'export_top_type'],
            ],
            'export_type' => [
                'label' => Yii::t('hipanel', 'Type'),
                'value' => static fn(Charge $charge): string => $charge->ftype,
            ],
            'export_top_type' => [
                'label' => Yii::t('hipanel', 'Payment Type'),
                'value' => static fn(Charge $charge): string => Yii::t('hipanel.finance.billTypes', explode(',', $charge->ftype)[0]),
            ],
            'sum' => [
                'class' => CurrencyColumn::class,
                'attribute' => 'sum',
                'sortAttribute' => 'sum',
                'colors' => ['danger' => 'warning'],
                'headerOptions' => ['class' => 'text-right'],
                'filter' => false,
                'contentOptions' => fn($model) => ['class' => 'text-right' . ($model->sum > 0 ? ' text-bold' : '')],
                'urlCallback' => fn($model) => $this->sumLink($model),
                'exportedColumns' => ['export_currency', 'export_sum'],
            ],
            'export_currency' => [
                'label' => Yii::t('hipanel', 'Currency'),
                'value' => fn($charge) => $charge->currency,
            ],
            'export_sum' => [
                'label' => Yii::t('hipanel', 'Sum'),
                'value' => fn($charge) => $this->plainSum($charge->sum),
            ],
            'name' => [
                'attribute' => 'name_ilike',
                'label' => Yii::t('hipanel', 'Object'),
                'format' => 'raw',
                'value' => function (Charge $model) {
                    $result = LinkToObjectResolver::widget([
                            'model' => $model,
                            'idAttribute' => 'object_id',
                            'typeAttribute' => 'class',
                            'labelAttribute' => 'name',
                        ]) . ($model->label ? " &ndash; $model->label" : '');

                    if ($model->commonObject->id !== null && $model->commonObject->id !== $model->latestCommonObject->id) {
                        $result .= ' ' . Html::tag(
                                'span',
                                Yii::t('hipanel:finance', 'Now it is in {objectLink}', [
                                    'objectLink' => LinkToObjectResolver::widget([
                                        'model' => $model->latestCommonObject,
                                        'idAttribute' => 'id',
                                        'labelAttribute' => 'name',
                                        'typeAttribute' => 'type',
                                    ]),
                                ]),
                                ['class' => 'badge', 'style' => 'background-color: #f89406;']
                            );
                    }

                    return $result;
                },
                'exportedColumns' => ['export_object_class', 'export_object_name', 'export_label'],
            ],
            'export_object_class' => [
                'label' => Yii::t('hipanel', 'Class'),
                'value' => fn($charge) => $charge->class,
            ],
            'export_object_name' => [
                'label' => Yii::t('hipanel', 'Object'),
                'value' => fn($charge) => $charge->name,
            ],
            'export_label' => [
                'label' => Yii::t('hipanel', 'Label'),
                'value' => fn($charge) => $charge->label,
            ],
            'quantity' => [
                'attribute' => 'quantity',
                'format' => 'raw',
                'filter' => false,
                'value' => fn(Charge $model) => $this->renderQuantity($model),
            ],
            'time' => [
                'filter' => false,
                'contentOptions' => ['class' => 'text-nowrap'],
                'value' => function ($model) {
                    [$date, $time] = explode(' ', $model->time, 2);

                    return $model->isMonthly() && $time === '00:00:00'
                        ? Yii::$app->formatter->asDate($date, 'LLLL y')
                        : Yii::$app->formatter->asDateTime($model->time);
                },
                'exportedValue' => fn($charge) => $charge->time,
            ],
            'is_payed' => [
                'attribute' => 'is_payed',
                'format' => 'raw',
                'enableSorting' => false,
                'filter' => $this->filterModel !== null
                    ? StaticCombo::widget([
                        'attribute' => 'is_payed',
                        'model' => $this->filterModel,
                        'data' => [
                            0 => Yii::t('hipanel:finance', 'Charge not paid'),
                            1 => Yii::t('hipanel:finance', 'Charge paid'),
                        ],
                        'hasId' => true,
                        'inputOptions' => ['id' => 'is_payed'],
                    ])
                    : false,
                'contentOptions' => ['class' => 'text-center'],
                'headerOptions' => ['class' => 'text-center'],
                'value' => static fn(Charge $model) => IconStateLabel::widget([
                    'model' => $model,
                    'attribute' => 'is_payed',
                    'icons' => ['fa-check', 'fa-times'],
                    'colors' => ['#00a65a', '#dd4b39'],
                    'messages' => [
                        Yii::t('hipanel:finance', 'Charge paid'),
                        Yii::t('hipanel:finance', 'Charge not paid'),
                    ],
                ]),
            ],
            'discount_sum' => [
                'attribute' => 'discount_sum',
                'headerOptions' => ['class' => 'text-right'],
                'value' => fn($charge): string => $charge->discount_sum
                    ? $this->formatter->asCurrency($charge->discount_sum, $charge->currency)
                    : '',
                'enableSorting' => true,
                'filter' => false,
                'exportedValue' => fn($charge) => $this->plainSum($charge->discount_sum),
            ],
            'bill_id' => [
                'attribute' => 'bill_id',
                'format' => 'raw',
                'enableSorting' => false,
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
                'value' => fn($charge): string => Html::a($charge->bill_id,
                    ['@bill/view', 'id' => $charge->bill_id],
                    ['target' => '_blank']),
            ],
            'id' => [
                'attribute' => 'id',
                'enableSorting' => false,
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
            ],
        ], $this->amountColumns());
    }

    /**
     * @param Charge $model
     * @return string|null
     */
    public function tariffLink(Charge $model): ?string
    {
        $canSeeLink = Yii::$app->user->can('plan.create');
        $tariff = Html::encode($model->tariff);

        return $canSeeLink ? Html::a($tariff, ['@plan/view', 'id' => $model->tariff_id]) : $tariff;
    }

    /**
     * @param Charge $charge
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

    public function sumLink(Charge $model): ?string
    {
        return Yii::$app->user->can('bill.read')
            ? Url::to(['@bill/view', 'id' => $model->bill_id, '#' => $model->id])
            : null;
    }
}
