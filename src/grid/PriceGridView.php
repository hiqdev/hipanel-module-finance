<?php

namespace hipanel\modules\finance\grid;

use hipanel\grid\RefColumn;
use hipanel\modules\finance\menus\PriceActionsMenu;
use hipanel\modules\finance\models\Price;
use hipanel\modules\finance\widgets\LinkToObjectResolver;
use hipanel\modules\finance\widgets\PriceType;
use hiqdev\yii2\menus\grid\MenuColumn;
use Yii;
use yii\bootstrap\Html;

class PriceGridView extends \hipanel\grid\BoxedGridView
{
    public function columns()
    {
        return array_merge(parent::columns(), [
            'plan' => [
                'format' => 'html',
                'filterAttribute' => 'plan_ilike',
                'filterOptions' => ['class' => 'narrow-filter'],
                'value' => function ($model) {
                    return Html::a($model->plan, ['@plan/view', 'id' => $model->plan_id]);
                },
            ],
            'price/unit' => [
                'label' => Yii::t('hipanel.finance.price', 'Price/unit'),
                'value' => function (Price $model) {
                    return Yii::$app->formatter->asCurrency($model->price, $model->currency)
                        . '/' . Yii::t('hipanel.finance.price', $model->getUnitOptions()[$model->unit]);
                }
            ],
            'object->type' => [
                'label' => Yii::t('hipanel', 'Object'),
                'format' => 'html',
                'value' => function (Price $model) {
                    $link = LinkToObjectResolver::widget([
                        'model' => $model->object,
                        'labelAttribute' => 'type',
                        'typeAttribute' => 'class_name'
                    ]);

                    return $link ?: Yii::t('hipanel.finance.price', 'Any');
                }
            ],
            'object->name' => [
                'label' => Yii::t('hipanel', 'Details'),
                'value' => function (Price $model) {
                    return $model->object->name;
                }
            ],
            'type' => [
                'class' => RefColumn::class,
                'attribute' => 'type',
                'filterAttribute' => 'type',
                'filterOptions' => ['class' => 'narrow-filter'],
                'format' => 'html',
                'gtype' => 'type,price',
                'findOptions' => [
                    'select' => 'name_label',
                    'pnames' => 'monthly,overuse',
                    'with_recursive' => 1,
                    'mapOptions' => ['from' => 'oname'],
                ],
                'value' => function ($model) {
                    return PriceType::widget(['model' => $model]);
                }
            ],
            'unit' => [
                'class' => RefColumn::class,
                'attribute' => 'unit',
                'filterAttribute' => 'unit',
                'filterOptions' => ['class' => 'narrow-filter'],
                'format' => 'html',
                'gtype' => 'type,unit',
                'findOptions' => [
                    'with_recursive' => 1,
                    'select' => 'name_label',
                    'mapOptions' => ['from' => 'name'],
                ],
            ],
            'currency' => [
                'class' => RefColumn::class,
                'attribute' => 'currency',
                'filterAttribute' => 'currency',
                'filterOptions' => ['class' => 'narrow-filter'],
                'format' => 'html',
                'gtype' => 'type,currency',
            ],
            'actions' => [
                'class' => MenuColumn::class,
                'menuClass' => PriceActionsMenu::class,
            ],
        ]);
    }
}
