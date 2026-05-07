<?php

use hipanel\modules\finance\grid\InstallmentPlanGridView;
use hipanel\modules\finance\grid\InstallmentPlanItemGridView;
use hipanel\modules\finance\menus\InstallmentPlanDetailMenu;
use hipanel\modules\finance\models\InstallmentPlan;
use hipanel\widgets\Box;
use hipanel\widgets\IndexPage;
use hipanel\widgets\MainDetails;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var InstallmentPlan $model
 * @var ArrayDataProvider $itemsDataProvider
 */

$this->title = Html::encode($model->serialno ?: $model->client);
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Installment plans'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="row">
    <div class="col-md-3">
        <div class="row">
            <div class="col-md-12">
                <?= MainDetails::widget([
                    'title' => $this->title,
                    'icon' => 'fa-credit-card',
                    'subTitle' => Html::encode($model->client),
                    'menu' => InstallmentPlanDetailMenu::widget(['model' => $model]),
                ]) ?>
            </div>
            <div class="col-md-12">
                <?php $box = Box::begin([
                    'renderBody' => false,
                    'bodyOptions' => ['class' => 'no-padding'],
                    'options' => ['class' => 'box-widget'],
                ]) ?>
                    <?php $box->beginHeader() ?>
                        <?= $box->renderTitle(Yii::t('hipanel', 'Details')) ?>
                    <?php $box->endHeader() ?>
                    <?php $box->beginBody() ?>
                        <div class="table-responsive">
                        <?= InstallmentPlanGridView::detailView([
                            'model' => $model,
                            'boxed' => false,
                            'columns' => [
                                'client', 'seller',
                                'serialno', 'model', 'device',
                                'state', 'since', 'till', 'quantity',
                                'expected_monthly_sum', 'charged_sum', 'left_sum', 'expected_sum',
                                'order_name', 'company_id', 'warranty_till',
                            ],
                        ]) ?>
                        </div>
                    <?php $box->endBody() ?>
                <?php $box->end() ?>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <?php $page = IndexPage::begin(['model' => $model, 'layout' => 'noSearch']) ?>

        <?php $page->beginContent('show-actions') ?>
            <h4 class="box-title" style="display: inline-block;">&nbsp;<?= Yii::t('hipanel:finance', 'Payments') ?></h4>
        <?php $page->endContent() ?>

        <?php $page->beginContent('table') ?>
            <?= InstallmentPlanItemGridView::widget([
                'boxed' => false,
                'dataProvider' => $itemsDataProvider,
                'columns' => array_filter([
                    'no', 'month', 'tariff_link', 'sum', 'charge_sum',
                    Yii::$app->user->can('bill.charges.read') ? 'charge_id' : null,
                    'bill_id',
                ]),
            ]) ?>
        <?php $page->endContent() ?>

        <?php $page->end() ?>
    </div>
</div>
