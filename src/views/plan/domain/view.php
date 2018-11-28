<?php

use hipanel\modules\finance\grid\DomainServicePriceGridView;
use hipanel\modules\finance\grid\DomainZonePriceGridView;
use hipanel\modules\finance\widgets\CreatePricesButton;
use hipanel\widgets\IndexPage;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use hipanel\modules\finance\models\DomainServicePrice;

/**
 * @var \yii\web\View $this
 * @var \hipanel\modules\finance\helpers\PlanInternalsGrouper $grouper
 * @var \hipanel\modules\finance\models\Plan $model
 * @var array[] $parentPrices
 * @var \hipanel\modules\finance\models\DomainZonePrice[][] $zonePrices
 * @var \hipanel\modules\finance\models\DomainServicePrice[] $servicePrices
 * @var \hipanel\modules\finance\models\DomainZonePrice[][] $parentZonePrices
 * @var \hipanel\modules\finance\models\DomainServicePrice[] $parentServicePrices
 * @var IndexPage $page
 */

[$zonePrices, $servicePrices] = $grouper->group();
[$parentZonePrices, $parentServicePrices] = $parentPrices;

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
        <?= DomainZonePriceGridView::widget([
            'boxed' => false,
            'emptyText' => Yii::t('hipanel.finance.price', 'No prices found'),
            'dataProvider' => (new ArrayDataProvider([
                'allModels' => $zonePrices,
                'pagination' => false,
            ])),
            'parentPrices' => $parentZonePrices,
            'columns' => [
                'name',
                'registration',
                'transfer',
                'renewal',
                'deleteInAgp',
                'restoringExpired',
                'restoringDeleted',
            ],
        ]) ?>
        <h4 class="box-title" style="display: inline-block;">&nbsp;
            <?= DomainServicePrice::getLabel() ?>
        </h4>
        <?= DomainServicePriceGridView::widget([
            'boxed' => false,
            'emptyText' => Yii::t('hipanel.finance.price', 'No prices found'),
            'dataProvider' => (new ArrayDataProvider([
                'allModels' => [$servicePrices],
                'pagination' => false,
            ])),
            'parentPrices' => $parentServicePrices,
            'columns' => [
                'purchase',
                'renewal',
            ],
        ])?>
    <?php $page->endBulkForm() ?>
<?php $page->endContent() ?>
