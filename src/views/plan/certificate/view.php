<?php

use hipanel\modules\finance\grid\CertificatePriceGridView;
use hipanel\modules\finance\widgets\CreatePricesButton;
use hipanel\widgets\IndexPage;
use yii\data\ArrayDataProvider;
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
        <?= CreatePricesButton::widget(compact('model')) ?>
    <?php endif ?>
    <?php if (Yii::$app->user->can('plan.update')) : ?>
        <?= Html::a(Yii::t('hipanel', 'Update'), ['@plan/update-prices', 'id' => $model->id], ['class' => 'btn btn-sm btn-warning']) ?>
    <?php endif ?>
<?php $page->endContent() ?>

<?php $page->beginContent('table') ?>
    <?php $page->beginBulkForm() ?>
        <?= CertificatePriceGridView::widget([
            'boxed' => false,
            'emptyText' => Yii::t('hipanel.finance.price', 'No prices found'),
            'dataProvider' => (new ArrayDataProvider([
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
