<?php

use hipanel\models\IndexPageUiOptions;
use hipanel\modules\finance\grid\InstallmentPlanGridLegend;
use hipanel\modules\finance\grid\InstallmentPlanGridView;
use hipanel\modules\finance\grid\InstallmentPlanRepresentations;
use hipanel\modules\finance\models\InstallmentPlanSearch;
use hipanel\widgets\AjaxModalWithTemplatedButton;
use hipanel\widgets\gridLegend\GridLegend;
use hipanel\widgets\IndexPage;
use yii\bootstrap\Modal;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var InstallmentPlanSearch $model
 * @var IndexPageUiOptions $uiModel
 * @var InstallmentPlanRepresentations $representationCollection
 * @var ActiveDataProvider $dataProvider
 */

$this->title = Yii::t('hipanel:finance', 'Installment plans');
$this->params['breadcrumbs'][] = $this->title;

?>

<?php $page = IndexPage::begin(['model' => $model, 'dataProvider' => $dataProvider]) ?>

    <?php $page->beginContent('legend') ?>
        <?= GridLegend::widget(['legendItem' => new InstallmentPlanGridLegend($model)]) ?>
    <?php $page->endContent() ?>

    <?php $page->beginContent('main-actions') ?>
        <?php if (Yii::$app->user->can('installment-plan.process')): ?>
            <?= Html::a('<i class="fa fa-refresh"></i>&nbsp;' . Yii::t('hipanel:finance', 'Process'), ['process'], [
                'class' => 'btn btn-sm btn-info',
                'data' => [
                    'method' => 'POST',
                    'confirm' => Yii::t('hipanel:finance', 'Are you sure you want to process all installment plans?'),
                ],
            ]) ?>
        <?php endif ?>
    <?php $page->endContent() ?>

    <?php $page->beginContent('sorter-actions') ?>
        <?= $page->renderSorter(['attributes' => ['id', 'since', 'till']]) ?>
    <?php $page->endContent() ?>

    <?php $page->beginContent('representation-actions') ?>
        <?= $page->renderRepresentations($representationCollection) ?>
    <?php $page->endContent() ?>

    <?php $page->beginContent('bulk-actions') ?>
        <?php if (Yii::$app->user->can('installment-plan.delete')): ?>
            <?= $page->renderBulkDeleteButton('delete', '<i class="fa fa-trash"></i>&nbsp;' . Yii::t('hipanel', 'Delete')) ?>
        <?php endif ?>
        <?php if (Yii::$app->user->can('installment-plan.restore')): ?>
            <?= $page->renderBulkButton('restore', '<i class="fa fa-undo"></i>&nbsp;' . Yii::t('hipanel', 'Restore'), [
                'color' => 'default',
                'confirm' => Yii::t('hipanel:finance', 'Are you sure you want to restore these installment plans?'),
            ]) ?>
        <?php endif ?>
        <?php if (Yii::$app->user->can('bill.create')): ?>
            <?= AjaxModalWithTemplatedButton::widget([
                'ajaxModalOptions' => [
                    'bulkPage'     => true,
                    'usePost'      => true,
                    'id'           => 'installment-plan-create-bill-modal',
                    'scenario'     => 'create-bill',
                    'actionUrl'    => ['create-bill'],
                    'handleSubmit' => false,
                    'size'         => Modal::SIZE_LARGE,
                    'header'       => Html::tag('h4', Yii::t('hipanel:finance', 'Create bill for installment plans'), ['class' => 'modal-title']),
                    'toggleButton' => [
                        'tag'   => 'button',
                        'label' => '<i class="fa fa-money"></i>&nbsp;' . Yii::t('hipanel:finance', 'Create bill'),
                        'class' => 'btn btn-sm btn-default',
                    ],
                ],
            ]) ?>
        <?php endif ?>
    <?php $page->endContent() ?>

    <?php $page->beginContent('table') ?>
        <?php $page->beginBulkForm() ?>
            <?= InstallmentPlanGridView::widget([
                'boxed' => false,
                'dataProvider' => $dataProvider,
                'filterModel' => $model,
                'columns' => $representationCollection->getByName($uiModel->representation)->getColumns(),
            ]) ?>
        <?php $page->endBulkForm() ?>
    <?php $page->endContent() ?>

<?php $page->end() ?>
