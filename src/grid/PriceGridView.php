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
use hipanel\grid\RefColumn;
use hipanel\modules\finance\grid\presenters\price\PricePresenter;
use hipanel\modules\finance\grid\presenters\price\PricePresenterFactory;
use hipanel\modules\finance\menus\PriceActionsMenu;
use hipanel\modules\finance\models\Price;
use hipanel\modules\finance\widgets\BillType;
use hipanel\modules\finance\widgets\LinkToObjectResolver;
use hiqdev\yii2\menus\grid\MenuColumn;
use Yii;
use yii\bootstrap\Html;

/**
 * Class PriceGridView.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class PriceGridView extends BoxedGridView
{
    public function __construct(
        readonly private PricePresenterFactory $presenterFactory,
        array $config = []
    )
    {
        parent::__construct($config);
    }

    public function columns()
    {
        return array_merge(parent::columns(), [
            'plan' => [
                'format' => 'raw',
                'filterAttribute' => 'plan_name_ilike',
                'filterOptions' => ['class' => 'narrow-filter'],
                'value' => function (Price $model) {
                    return Html::a(Html::encode($model->plan->name), ['@plan/view', 'id' => $model->plan->id]);
                },
            ],
            'price' => [
                'label' => Yii::t('hipanel.finance.price', 'Price'),
                'format' => 'raw',
                'value' => fn(Price $model) => $this->presenterFactory->build($model::class)->renderPrice($model),
            ],
            'old_price' => [
                'label' => Yii::t('hipanel.finance.price', 'Old price'),
                'format' => 'raw',
                'value' => function (Price $model): string {
                    $presenter = $this->presenterFactory->build($model::class);

                    return $presenter
                        ->setPriceAttribute('old_price')
                        ->renderPrice($model);
                },
            ],
            'object->name_clear' => [
                'label' => Yii::t('hipanel', 'Object'),
                'value' => function (Price $model) {
                    return $model->object->name ?: Yii::t('hipanel.finance.price', 'Any');
                },
            ],
            'object->name' => [
                'label' => Yii::t('hipanel', 'Object'),
                'format' => 'raw',
                'value' => function (Price $model) {
                    $link = LinkToObjectResolver::widget([
                        'model' => $model->object,
                        'labelAttribute' => 'name',
                    ]);

                    return Yii::t('hipanel.finance.price', $link) ?: Yii::t('hipanel.finance.price', 'Any');
                },
            ],
            'object->name-any' => [
                'label' => Yii::t('hipanel', 'Object'),
                'value' => function (Price $model) {
                    return Yii::t('hipanel.finance.price', 'Any');
                },
            ],
            'object->label' => [
                'class' => RefColumn::class,
                'attribute' => 'object_name_ilike',
                'label' => Yii::t('hipanel', 'Details'),
                'i18nDictionary' => 'hipanel.finance.plan',
                'value' => function (Price $model) {
                    return $model->object->label;
                },
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
                'value' => static fn($model) => BillType::widget(['model' => $model]),
            ],
            'unit' => [
                'class' => RefColumn::class,
                'attribute' => 'unit',
                'filterAttribute' => 'unit',
                'filterOptions' => ['class' => 'narrow-filter'],
                'gtype' => 'type,unit',
                'i18nDictionary' => 'hipanel.finance.units',
                'findOptions' => [
                    'with_recursive' => 1,
                    'select' => 'name_label',
                    'mapOptions' => ['from' => 'name'],
                ],
                'value' => function (Price $model) {
                    return $model->getUnitLabel();
                },
            ],
            'currency' => [
                'class' => RefColumn::class,
                'attribute' => 'currency',
                'filterAttribute' => 'currency',
                'filterOptions' => ['class' => 'narrow-filter'],
                'gtype' => 'type,currency',
            ],
            'actions' => [
                'class' => MenuColumn::class,
                'menuClass' => PriceActionsMenu::class,
            ],
            'info' => [
                'format' => 'raw',
                'label' => Yii::t('hipanel', 'Details'),
                'value' => function (Price $model) {
                    return $this->presenterFactory->build($model::class)->renderInfo($model);
                },
            ],
            'old_quantity' => [
                'format' => 'raw',
                'label' => Yii::t('hipanel.finance.price', 'Old quantity'),
                'value' => function (Price $model) {
                    return $this->presenterFactory->build($model::class)->renderInfo($model, 'old_quantity');
                },
            ],
            'value' => [
                'class' => ValueColumn::class,
                'visible' => Yii::$app->user->can('bill.charges.read'),
            ],
            'rate' => [
                'label' => Yii::t('hipanel.finance.price', 'Referral rate'),
                'attribute' => 'rate',
                'filter' => false,
            ],
        ]);
    }
}
