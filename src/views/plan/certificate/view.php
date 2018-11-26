<?php

use hipanel\widgets\AjaxModal;
use hipanel\widgets\IndexPage;
use yii\bootstrap\Modal;
use yii\helpers\Html;

/**
 * @var \yii\web\View $this
 * @var \hipanel\modules\finance\models\Plan $model
 * @var \hipanel\modules\finance\helpers\PlanInternalsGrouper $grouper
 * @var \hipanel\modules\finance\models\CertificatePrice[][] $parentPrices
 * @var IndexPage $page
 */

$prices = $grouper->group();

?>

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
    <?php if (Yii::$app->user->can('plan.update')) : ?>
        <?= Html::a(Yii::t('hipanel', 'Update'), ['@plan/update-prices', 'id' => $model->id], ['class' => 'btn btn-sm btn-warning']) ?>
    <?php endif ?>
<?php $page->endContent() ?>

<?php $page->beginContent('table') ?>
    <?php $page->beginBulkForm() ?>
        <?= \hipanel\modules\finance\grid\CertificatePriceGridView::widget([
            'boxed' => false,
            'emptyText' => Yii::t('hipanel.finance.price', 'No prices found'),
            'dataProvider' => (new \yii\data\ArrayDataProvider([
                'allModels' => $prices,
                'pagination' => false,
            ])),
            'parentPrices' => $parentPrices,
            'filterModel' => $model,
            'columns' => [
                'certificate',
                'purchase',
                'renewal',
            ],
        ]) ?>
    <?php $page->endBulkForm() ?>
<?php $page->endContent() ?>
