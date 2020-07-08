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

use hipanel\modules\finance\menus\SalePricesActionsMenu;
use hipanel\modules\finance\models\FakeSale;
use hipanel\modules\finance\models\Sale;
use hipanel\widgets\Label;
use hiqdev\yii2\menus\grid\MenuColumn;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;

/**
 * Class SalesInPlanGridView.
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
                'value' => function (Sale $sale, $key) {
                    if ($sale instanceof FakeSale) {
                        return Label::widget([
                            'label' => Yii::t('hipanel:finance:sale', 'Not sold'),
                            'color' => 'danger',
                        ]);
                    }

                    foreach ($this->pricesBySoldObject[$key] ?? [] as $price) {
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
                        . '&nbsp;<span class="caret"></span>',
                ],
            ],
            'estimate_placeholder' => [
                'contentOptions' => [
                    'class' => 'total-per-object-cell',
                ],
                'format' => 'raw',
                'value' => function () {
                    return  Html::tag('span', Yii::t('hipanel:finance', 'Total:')) . '&nbsp;&nbsp;' .
                            Html::tag('span', '', ['class' => 'total-per-object']);
                },
            ],
        ]);
    }

    private function initAfterRow()
    {
        $this->afterRow = function (Sale $sale, $key) {
            $prices = $this->pricesBySoldObject[$key] ?? [];
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
                'columns' => array_filter([
                    'checkbox',
                    Yii::$app->user->can('part.read') ? 'object->name' : 'object->name_clear',
                    'type',
                    Yii::$app->user->can('part.read') ? 'info' : null,
                    Yii::$app->user->can('plan.update') ? 'price' : null,
                    'value',
                    'note',
                ]),
            ]);
        };
    }
}
