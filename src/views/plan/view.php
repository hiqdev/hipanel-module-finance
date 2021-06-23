<?php

use hipanel\modules\finance\grid\PlanGridView;
use hipanel\modules\finance\helpers\PlanInternalsGrouper;
use hipanel\modules\finance\menus\PlanDetailMenu;
use hipanel\modules\finance\models\Plan;
use hipanel\modules\finance\models\PriceHistory;
use hipanel\modules\finance\widgets\PriceHistoryWidget;
use hipanel\widgets\CustomAttributesViewer;
use hipanel\widgets\IndexPage;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var Plan $model
 * @var PlanInternalsGrouper $grouper
 * @var array $parentPrices
 * @var PriceHistory[] $plansHistory
 */
$this->title = $model->name ? Html::encode($model->name) : '&nbsp;';
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Tariff plans'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss(<<<'CSS'
.profile-block {
    text-align: center;
}

.total-per-object-cell {
    display: flex;
}

.total-per-currency {
    display: block;
}

.text-gray {
    color: gray !important;
}

.prices-cell {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
}
.left-table-item {
    text-align: left
}
.right-table-item {
    text-align: right;
    padding-left: 6px;
}

CSS
);

?>

<div class="row">
    <div class="col-md-3">
        <div class="box box-solid">
            <div class="box-body no-padding">
                <div class="profile-user-img text-center">
                    <i class="fa fa-bar-chart fa-5x"></i>
                </div>
                <p class="text-center">
                    <span class="profile-user-role">
                        <?= $this->title ?>
                    </span>
                    <br>
                    <span class="profile-user-name"><?= $model->type ?></span>
                </p>

                <div class="profile-usermenu" style="border-top: 1px solid #f4f4f4;">
                    <?= PlanDetailMenu::widget(['model' => $model]) ?>
                </div>
            </div>
            <div class="box-footer no-padding">
                <?= PlanGridView::detailView([
                    'model' => $model,
                    'boxed' => false,
                    'columns' => array_filter([
                        'simple_name',
                        'monthly',
                        'client',
                        'type',
                        'state',
                        'note',
                    ]),
                ]) ?>
            </div>
            <div class="box-header">
                <h4 class="box-title">
                    <?= Yii::t('hipanel:finance', 'Attributes') ?>
                </h4>
            </div>
            <div class="box-footer no-padding">
                <?= CustomAttributesViewer::widget(['owner' => $model]) ?>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="col">
            <div class="row-md-12">
                <?php $page = IndexPage::begin(['model' => $model, 'layout' => $model->type === Plan::TYPE_CALCULATOR ? 'resourceDetail' : 'noSearch']) ?>
                <?php if ($model->isKnownType()): ?>
                    <?php $page->beginContent('show-actions') ?>
                    <h4 class="box-title" style="display: inline-block;">
                        &nbsp;<?= Yii::t('hipanel:finance', 'Prices') ?></h4>
                    <?php $page->endContent() ?>
                    <?= $this->render($model->type . '/view', compact('model', 'grouper', 'page', 'parentPrices')) ?>
                <?php else: ?>
                    <?php $page->beginContent('table') ?>
                    <div class="col-md-12">
                        <h2><?= Yii::t('hipanel:finance', 'This plan type viewing is not implemented yet') ?></h2>
                    </div>
                    <?php $page->endContent() ?>
                <?php endif; ?>

                <?php $page::end() ?>
            </div>
            <?php if (Yii::$app->user->can('plan.update')): ?>
                <div class="row-md-12">
                    <?= PriceHistoryWidget::widget([
                        'model' => $model,
                    ]) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
