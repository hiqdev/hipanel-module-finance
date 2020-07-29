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
                    return $this->presenterFactory->build(\get_class($model))->renderPrice($model);
                },
            ],
            'old_price' => [
                'label' => Yii::t('hipanel.finance.price', 'Old price'),
                'format' => 'raw',
                'value' => function (Price $model): string {
                    /** @var PricePresenter $presenter */
                    $presenter = $this->presenterFactory->build(\get_class($model));
                    return $presenter
                            ->setPriceAttribute('old_price')
                            ->renderPrice($model);
                },
            ],
            'object->name_clear' => [
                'label' => Yii::t('hipanel', 'Object'),
                'format' => 'raw',
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

                    return $link ?: Yii::t('hipanel.finance.price', 'Any');
                },
            ],
            'object->name-any' => [
                'label' => Yii::t('hipanel', 'Object'),
                'value' => function (Price $model) {
                    return Yii::t('hipanel.finance.price', 'Any');
                },
            ],
            'object->label' => [
                'format' => 'raw',
                'label' => Yii::t('hipanel', 'Details'),
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
                'value' => function ($model) {
                    return BillType::widget(['model' => $model]);
                },
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
                },
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
            'info' => [
                'format' => 'raw',
                'label' => Yii::t('hipanel', 'Details'),
                'value' => function (Price $model) {
                    return $this->presenterFactory->build(\get_class($model))->renderInfo($model);
                },
            ],
            'value' => [
                'class' => ValueColumn::class,
            ],
            'rate' => [
                'label' => Yii::t('hipanel.finance.price', 'Referral rate'),
                'attribute' => 'rate',
            ],
        ]);
    }
}
