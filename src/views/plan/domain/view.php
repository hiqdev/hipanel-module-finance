<?php

use hipanel\widgets\AjaxModal;
use hipanel\widgets\IndexPage;
use yii\bootstrap\Modal;
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
    <?= AjaxModal::widget([
        'id' => 'create-prices-modal',
        'header' => Html::tag('h4', Yii::t('hipanel.finance.price', 'Create prices'), ['class' => 'modal-title']),
        'scenario' => 'create-prices',
        'actionUrl' => ['@plan/suggest-prices-modal', 'id' => $model->id],
        'size' => Modal::SIZE_SMALL,
        'toggleButton' => ['label' => Yii::t('hipanel', 'Create'), 'class' => 'btn btn-sm btn-success'],
    ]) ?>
    <?= Html::a(Yii::t('hipanel', 'Update'), ['@plan/update-prices', 'id' => $model->id], ['class' => 'btn btn-sm btn-warning']) ?>
<?php $page->endContent() ?>

<?php $page->beginContent('table') ?>
    <?php $page->beginBulkForm() ?>
        <?= \hipanel\modules\finance\grid\DomainZonePriceGridView::widget([
            'boxed' => false,
            'emptyText' => Yii::t('hipanel.finance.price', 'No prices found'),
            'dataProvider' => (new \yii\data\ArrayDataProvider([
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
            <?= DomainServicePrice::getType() ?>
        </h4>
        <?= \hipanel\modules\finance\grid\DomainServicePriceGridView::widget([
            'boxed' => false,
            'emptyText' => Yii::t('hipanel.finance.price', 'No prices found'),
            'dataProvider' => (new \yii\data\ArrayDataProvider([
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
