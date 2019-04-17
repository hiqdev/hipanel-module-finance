<?php

use hipanel\modules\finance\grid\PriceGridView;
use hipanel\modules\finance\helpers\PriceSort;
use hipanel\modules\finance\widgets\CreatePricesButton;
use hipanel\widgets\IndexPage;
use yii\data\ArrayDataProvider;

/**
 * @var \yii\web\View $this
 * @var \hipanel\modules\finance\models\Plan $model
 * @var IndexPage $page
 */
?>

<?php $page->beginContent('bulk-actions') ?>
    <?php if (Yii::$app->user->can('price.update')) : ?>
        <?= $page->renderBulkButton('@price/update', Yii::t('hipanel', 'Update'), ['color' => 'warning']) ?>
    <?php endif ?>
    <?php if (Yii::$app->user->can('price.delete')) : ?>
        <?= $page->renderBulkDeleteButton('@price/delete') ?>
    <?php endif ?>
<?php $page->endContent() ?>

<?php $page->beginContent('main-actions') ?>
    <?php if (Yii::$app->user->can('plan.create')) : ?>
       <?= CreatePricesButton::widget(compact('model')) ?>
    <?php endif ?>
<?php $page->endContent() ?>

<?php $page->beginContent('table') ?>
    <?php $page->beginBulkForm() ?>
        <?= PriceGridView::widget([
            'boxed' => false,
            'emptyText' => Yii::t('hipanel.finance.price', 'No prices found'),
            'dataProvider' => new ArrayDataProvider([
                'allModels' => PriceSort::anyPrices()->values($model->prices, true),
                'pagination' => false,
            ]),
            'columns' => [
                'checkbox',
                'object->name',
                'object->label',
                'type',
                'price',
                'note',
            ],
        ]) ?>
    <?php $page->endBulkForm() ?>
<?php $page->endContent() ?>
