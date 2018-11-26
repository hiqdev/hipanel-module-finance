<?php

use hipanel\widgets\AjaxModal;
use hipanel\widgets\IndexPage;
use yii\bootstrap\Modal;
use yii\helpers\Html;

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
        <?= AjaxModal::widget([
            'id' => 'create-prices-modal',
            'header' => Html::tag('h4', Yii::t('hipanel.finance.price', 'Create prices'), ['class' => 'modal-title']),
            'scenario' => 'create-prices',
            'actionUrl' => ['@plan/suggest-prices-modal', 'id' => $model->id],
            'size' => Modal::SIZE_SMALL,
            'toggleButton' => ['label' => Yii::t('hipanel', 'Create'), 'class' => 'btn btn-sm btn-success'],
        ]) ?>
    <?php endif ?>
<?php $page->endContent() ?>

<?php $page->beginContent('table') ?>
    <?php $page->beginBulkForm() ?>
        <?= \hipanel\modules\finance\grid\PriceGridView::widget([
            'boxed' => false,
            'emptyText' => Yii::t('hipanel.finance.price', 'No prices found'),
            'dataProvider' => (new \yii\data\ArrayDataProvider([
                'allModels' => $model->prices,
                'pagination' => false,
            ])),
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
