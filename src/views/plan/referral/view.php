<?php

use hipanel\modules\finance\grid\ReferralPriceGridView;
use hipanel\modules\finance\helpers\PlanInternalsGrouper;
use hipanel\modules\finance\models\Plan;
use hipanel\modules\finance\models\Sale;
use hipanel\modules\finance\models\Price;
use hipanel\modules\finance\widgets\CreateReferralPricesButton;
use hipanel\widgets\IndexPage;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Plan $model
 * @var View $this
 * @var IndexPage $page
 * @var PlanInternalsGrouper $grouper
 * @var Sale[] $salesByObject
 * @var Price[][] $prices
 */

?>

<?php if (!$model->your_tariff) : ?>
    <?php $page->beginContent('main-actions') ?>
        <?php if (Yii::$app->user->can('plan.create')) : ?>
            <?= CreateReferralPricesButton::widget(compact('model')) ?>
        <?php endif ?>
        <?php if (Yii::$app->user->can('plan.update')) : ?>
            <?= Html::a(Yii::t('hipanel', 'Update'), ['@plan/update-prices', 'id' => $model->id], ['class' => 'btn btn-sm btn-warning']) ?>
        <?php endif ?>
    <?php $page->endContent() ?>
<?php endif ?>

<?php $page->beginContent('table') ?>
    <?php $page->beginBulkForm() ?>
        <?= ReferralPriceGridView::widget([
            'boxed' => false,
            'emptyText' => Yii::t('hipanel.finance.price', 'No prices found'),
            'dataProvider' => (new ArrayDataProvider([
                'allModels' => $model->prices,
                'pagination' => false,
            ])),
            'filterModel' => $model,
            'columns' => [
                'object->name-any',
                'rate' => [
                    'label' => Yii::t('hipanel.finance.price', 'Referral rate'),
                    'attribute' => 'rate',
                    'filter' => false,
                ]
            ],
        ]) ?>
    <?php $page->endBulkForm() ?>
<?php $page->endContent() ?>
