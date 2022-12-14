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
use hipanel\helpers\Url;
use hipanel\modules\client\grid\ClientColumn;
use hipanel\modules\finance\logic\bill\QuantityFormatterFactoryInterface;
use hipanel\modules\finance\models\Charge;
use hipanel\modules\finance\models\ChargeSearch;
use hipanel\modules\finance\widgets\BillType;
use hipanel\modules\finance\widgets\BillTypeFilter;
use hipanel\modules\finance\widgets\LinkToObjectResolver;
use hipanel\widgets\IconStateLabel;
use hiqdev\combo\StaticCombo;
use hiqdev\higrid\DataColumn;
use Yii;
use yii\helpers\Html;

/**
 * Class ChargeGridView.
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
            'label' => [
                'attribute' => 'label_ilike',
                'sortAttribute' => 'label_ilike',
                'label' => Yii::t('hipanel', 'Description'),
                'value' => function (Charge $model): string {
                    return $model->label ?? '';
                },
            ],
            'tariff' => [
                'attribute' => 'tariff_id',
                'label' => Yii::t('hipanel', 'Plan'),
                'filter' => false,
                'format' => 'raw',
                'value' => function (Charge $model): string {
                    return $this->tariffLink($model);
                },
            ],
            'type_label' => [
                'label' => Yii::t('hipanel', 'Type'),
                'headerOptions' => ['class' => 'text-right', 'style' => 'min-width: 15em; max-width: 25em'],
                'format' => 'raw',
                'value' => function (Charge $model) {
                    return BillType::widget([
                        'model' => $model,
                        'field' => 'ftype',
                        'labelField' => 'type_label',
                    ]);
                },
                'filterAttribute' => 'type',
                'filter' => function (DataColumn $column, ChargeSearch $filterModel): string {
                    return BillTypeFilter::widget([
                        'attribute' => 'type_id',
                        'model' => $filterModel,
                    ]);
                },
            ],
            'sum' => [
                'class' => CurrencyColumn::class,
                'attribute' => 'sum',
                'sortAttribute' => 'sum',
                'colors' => ['danger' => 'warning'],
                'headerOptions' => ['class' => 'text-right'],
                'filter' => false,
                'contentOptions' => function ($model) {
                    return ['class' => 'text-right' . ($model->sum > 0 ? ' text-bold' : '')];
                },
                'urlCallback' => function ($model) {
                    return $this->sumLink($model);
                },
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
                                    'model'          => $model->latestCommonObject,
                                    'idAttribute'    => 'id',
                                    'labelAttribute' => 'name',
                                    'typeAttribute'  => 'type',
                                ]),
                            ]),
                            ['class' => 'badge', 'style' => 'background-color: #f89406;']
                        );
                    }

                    return $result;
                },
            ],
            'quantity' => [
                'attribute' => 'quantity',
                'format' => 'raw',
                'filter' => false,
                'value' => function (Charge $model) {
                    return $this->renderQuantity($model);
                },
            ],
            'time' => [
                'filter' => false,
                'contentOptions' => ['class' => 'text-nowrap'],
                'value' => function ($model) {
                    list($date, $time) = explode(' ', $model->time, 2);

                    return $model->isMonthly() && $time === '00:00:00'
                        ? Yii::$app->formatter->asDate($date, 'LLLL y')
                        : Yii::$app->formatter->asDateTime($model->time);
                },
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
                'value' => static function (Charge $model) {
                    return IconStateLabel::widget([
                        'model' => $model,
                        'attribute' => 'is_payed',
                        'icons' => ['fa-check', 'fa-times'],
                        'colors' => ['#00a65a', '#dd4b39'],
                        'messages' => [
                            Yii::t('hipanel:finance', 'Charge paid'),
                            Yii::t('hipanel:finance', 'Charge not paid'),
                        ],
                    ]);
                }
            ]
        ]);
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
