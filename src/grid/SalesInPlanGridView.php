<?php

namespace hipanel\modules\finance\grid;

use hipanel\modules\finance\menus\SalePricesActionsMenu;
use hipanel\modules\finance\models\Sale;
use hiqdev\yii2\menus\grid\MenuColumn;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ArrayDataProvider;

/**
 * Class SalesInPlanGridView
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class SalesInPlanGridView extends SaleGridView
{
    /**
     * @var array
     */
    public $pricesBySoldObject;

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();

        if ($this->pricesBySoldObject === null) {
            throw new InvalidConfigException("Property 'pricesBySoldObject' must be set");
        }
        if (empty($this->afterRow)) {
            $this->initAfterRow();
        }
    }

    public function columns()
    {
        return array_merge(parent::columns(), [
            'object_label' => [
                'format' => 'raw',
                'value' => function (Sale $sale) {
                    $prices = $this->pricesBySoldObject[$sale->object_id ?? $sale->tariff_id] ?? [];
                    foreach ($prices as $price) {
                        if ($price->object->id === $sale->object_id) {
                            return $price->object->label;
                        }
                    }

                    return '';
                },
            ],
            'price_related_actions' => [
                'class' => MenuColumn::class,
                'menuClass' => SalePricesActionsMenu::class,
                'menuButtonOptions' => [
                    'icon' => '<i class="fa fa-plus"></i>&nbsp;'
                        . Yii::t('hipanel.finance.price', 'Prices')
                        . '&nbsp;<span class="caret"></span>'
                ]
            ]
        ]);
    }

    private function initAfterRow()
    {
        $this->afterRow = function (Sale $sale) {
            $prices = $this->pricesBySoldObject[$sale->object_id ?? $sale->tariff_id];
            if (empty($prices)) {
                return '';
            }

            return PriceGridView::widget([
                'boxed' => false,
                'showHeader' => true,
                'showFooter' => false,
                'options' => [
                    'tag' => 'tr',
                    'id' => crc32($sale->id ?? microtime(true)),
                ],
                'layout' => '<td colspan="' . \count($this->columns) . '">{items}</td>',
                'emptyText' => Yii::t('hipanel.finance.price', 'No prices found'),
                'dataProvider' => new ArrayDataProvider([
                    'allModels' => $prices,
                    'pagination' => false,
                ]),
                'columns' => [
                    'checkbox',
                    'object->name',
                    'type',
                    'info',
                    'price',
                    'note',
                ],
            ]);
        };
    }
}
