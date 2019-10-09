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
use hipanel\modules\client\grid\ClientColumn;
use hipanel\modules\finance\logic\bill\QuantityFormatterFactoryInterface;
use hipanel\modules\finance\models\Charge;
use hipanel\modules\finance\models\ChargeSearch;
use hipanel\modules\finance\widgets\BillType;
use hipanel\modules\finance\widgets\BillTypeFilter;
use hipanel\modules\finance\widgets\LinkToObjectResolver;
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
                'value' => function (Charge $model): string {
                    return $model->tariff;
                },
            ],
            'type_label' => [
                'label' => Yii::t('hipanel', 'Type'),
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
                        'options' => ['class' => 'form-control text-right', 'style' => 'max-width: 12em'],
                        'attribute' => 'ftype',
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
                'format' => 'raw',
                'filter' => false,
                'contentOptions' => ['class' => 'text-nowrap'],
                'value' => function ($model) {
                    list($date, $time) = explode(' ', $model->time, 2);

                    return $model->isMonthly() && $time === '00:00:00'
                        ? Yii::$app->formatter->asDate($date, 'LLLL y')
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
