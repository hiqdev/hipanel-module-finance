<?php

namespace hipanel\modules\finance\grid;

use hipanel\grid\RefColumn;
use hipanel\modules\finance\grid\presenters\price\PricePresenterFactory;
use hipanel\modules\finance\menus\PriceActionsMenu;
use hipanel\modules\finance\models\Price;
use hipanel\modules\finance\widgets\LinkToObjectResolver;
use hipanel\modules\finance\widgets\PriceType;
use hiqdev\yii2\menus\grid\MenuColumn;
use Yii;
use yii\bootstrap\Html;

/**
 * Class PriceGridView
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class PriceGridView extends \hipanel\grid\BoxedGridView
{
    /**
     * @var \hipanel\modules\finance\grid\presenters\price\PricePresenterFactory
     */
    private $presenterFactory;

    public function __construct(PricePresenterFactory $presenterFactory, array $config = [])
    {
        parent::__construct($config);
        $this->presenterFactory = $presenterFactory;
    }

    public function columns()
    {
        return array_merge(parent::columns(), [
            'plan' => [
                'format' => 'raw',
                'filterAttribute' => 'plan_name_ilike',
                'filterOptions' => ['class' => 'narrow-filter'],
                'value' => function (Price $model) {
                    return Html::a($model->plan->name, ['@plan/view', 'id' => $model->plan->id]);
                },
            ],
            'price' => [
                'label' => Yii::t('hipanel.finance.price', 'Price'),
                'format' => 'raw',
                'value' => function (Price $model) {
                    return $this->presenterFactory->build(get_class($model))->renderPrice($model);
                }
            ],
            'object->name' => [
                'label' => Yii::t('hipanel', 'Object'),
                'format' => 'raw',
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
                'format' => 'raw',
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
                'format' => 'raw',
                'gtype' => 'type,bill',
                'findOptions' => [
                    'select' => 'name',
                    'pnames' => 'monthly,overuse',
                    'with_recursive' => 1,
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
                'format' => 'raw',
                'gtype' => 'type,unit',
                'i18nDictionary' => 'hipanel.finance.units',
                'findOptions' => [
                    'with_recursive' => 1,
                    'select' => 'name_label',
                    'mapOptions' => ['from' => 'name'],
                ],
                'value' => function (Price $model) {
                    return $model->getUnitLabel();
                }
            ],
            'currency' => [
                'class' => RefColumn::class,
                'attribute' => 'currency',
                'filterAttribute' => 'currency',
                'filterOptions' => ['class' => 'narrow-filter'],
                'format' => 'raw',
                'gtype' => 'type,currency',
            ],
            'actions' => [
                'class' => MenuColumn::class,
                'menuClass' => PriceActionsMenu::class,
            ],
        ]);
    }
}
