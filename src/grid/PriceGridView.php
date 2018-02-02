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
                'filterAttribute' => 'plan_name_ilike',
                'filterOptions' => ['class' => 'narrow-filter'],
                'value' => function (Price $model) {
                    return Html::a($model->plan->name, ['@plan/view', 'id' => $model->plan->id]);
                },
            ],
            'price/unit' => [
                'label' => Yii::t('hipanel.finance.price', 'Price/unit'),
                'value' => function (Price $model) {
                    return Yii::$app->formatter->asCurrency($model->price, $model->currency)
                        . '/' . Yii::t('hipanel.finance.price', $model->getUnitOptions()[$model->unit]);
                }
            ],
            'object->name' => [
                'label' => Yii::t('hipanel', 'Object'),
                'format' => 'html',
                'value' => function (Price $model) {
                    $link = LinkToObjectResolver::widget([
                        'model' => $model->object,
                        'labelAttribute' => 'name',
                    ]);

                    return $link ?: Yii::t('hipanel.finance.price', 'Any');
                }
            ],
            'object->name-any' => [
                'label' => Yii::t('hipanel', 'Object'),
                'value' => function (Price $model) {
                    return Yii::t('hipanel.finance.price', 'Any');
                }
            ],
            'object->label' => [
                'label' => Yii::t('hipanel', 'Details'),
                'value' => function (Price $model) {
                    return $model->object->label;
                }
            ],
            'type' => [
                'class' => RefColumn::class,
                'label' => Yii::t('hipanel', 'Type'),
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
