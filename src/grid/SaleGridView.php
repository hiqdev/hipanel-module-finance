<?php

namespace hipanel\modules\finance\grid;

use hipanel\modules\client\grid\ClientColumn;
use hipanel\modules\finance\menus\PriceActionsMenu;
use hipanel\modules\finance\menus\SalePricesActionsMenu;
use hipanel\modules\finance\models\FakeSale;
use hipanel\modules\finance\models\Sale;
use hipanel\modules\finance\widgets\LinkToObjectResolver;
use hiqdev\yii2\menus\grid\MenuColumn;
use Yii;
use yii\helpers\Html;

class SaleGridView extends \hipanel\grid\BoxedGridView
{
    public function columns()
    {
        return array_merge(parent::columns(), [
            'tariff' => [
                'format' => 'html',
                'value' => function ($model) {
                    return Html::a($model->tariff, ['@tariff/view', 'id' => $model->tariff_id]);
                },
                'enableSorting' => false,
            ],
            'time' => [
                'format' => ['datetime'],
                'filter' => false,
                'contentOptions' => ['class' => 'text-nowrap'],
            ],
            'seller' => [
                'class' => ClientColumn::class,
                'idAttribute' => 'seller_id',
                'attribute' => 'seller_id',
                'nameAttribute' => 'seller',
                'enableSorting' => false,
            ],
            'buyer' => [
                'class' => ClientColumn::class,
                'idAttribute' => 'buyer_id',
                'attribute' => 'buyer_id',
                'nameAttribute' => 'buyer',
                'enableSorting' => false,
            ],
            'object_v' => [
                'label' => Yii::t('hipanel:finance:sale', 'Object'),
                'format' => 'html',
                'value' => function ($model) {
                    $html = Html::beginTag('div', ['class' => 'sale-flex-cnt']);
                    $html .= LinkToObjectResolver::widget([
                        'model' => $model,
                        'typeAttribute' => 'tariff_type',
                        'idAttribute' => 'object_id',
                    ]);
                    $html .= Html::endTag('div');

                    return $html;
                }
            ],
            'object' => [
                'format' => 'raw',
                'filterAttribute' => 'object_like',
                'enableSorting' => false,
                'value' => function (Sale $model) {
                    if ($model instanceof FakeSale) {
                        return $model->object;
                    }

                    $html = Html::beginTag('div', ['class' => 'sale-flex-cnt']);
                    $html .= LinkToObjectResolver::widget([
                        'model' => $model,
                        'typeAttribute' => 'tariff_type',
                        'idAttribute' => 'object_id',
                    ]);
                    $html .= Html::a(Yii::t('hipanel:finance:sale', 'More'), ['@sale/view', 'id' => $model->id], ['class' => 'btn btn-xs btn-default btn-flat']);
                    $html .= Html::endTag('div');

                    return $html;
                }
            ],
            'object_link' => [
                'attribute' => 'object',
                'format' => 'raw',
                'filterAttribute' => 'object_like',
                'enableSorting' => false,
                'value' => function (Sale $model) {
                    if ($model instanceof FakeSale) {
                        return $model->object;
                    }

                    return LinkToObjectResolver::widget([
                        'model' => $model,
                        'typeAttribute' => 'tariff_type',
                        'idAttribute' => 'object_id',
                    ]);
                }
            ],
            'price_related_actions' => [
                'class' => MenuColumn::class,
                'menuClass' => SalePricesActionsMenu::class,
            ]
        ]);
    }
}
