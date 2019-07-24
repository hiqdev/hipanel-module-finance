<?php

use hipanel\modules\finance\grid\SalesInPlanGridView;
use hipanel\modules\finance\models\Price;
use hipanel\modules\finance\models\Sale;
use hipanel\modules\finance\widgets\CreatePricesButton;
use hipanel\widgets\IndexPage;
use yii\data\ArrayDataProvider;

/**
 * @var \yii\web\View $this
 * @var \hipanel\modules\finance\models\Plan $model
 * @var IndexPage $page
 * @var \hipanel\modules\finance\helpers\PlanInternalsGrouper $grouper
 * @var Sale[] $salesByObject
 * @var Price[][] $pricesByMainObject
 */
[$salesByObject, $pricesByMainObject] = $grouper->group();

?>

<?php if (!$model->your_tariff) : ?>
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
<?php endif ?>

<?php $page->beginContent('table') ?>
<?php $page->beginBulkForm() ?>
    <?= SalesInPlanGridView::widget([
        'boxed' => false,
        'showHeader' => false,
        'pricesBySoldObject' => $pricesByMainObject,
        'dataProvider' => new ArrayDataProvider([
            'allModels' => $salesByObject,
            'pagination' => false,
        ]),
        'summaryRenderer' => function () {
            return ''; // remove unnecessary summary
        },
        'columns' => [
            'object_link',
            'object_label',
            'seller',
            'buyer',
            'time',
            'price_related_actions',
        ],
    ]) ?>
<?php $page->endBulkForm() ?>
<?php $page->endContent() ?>
