<?php

use hipanel\helpers\Url;
use hipanel\modules\finance\grid\PlanGridView;
use hipanel\modules\finance\menus\PlanDetailMenu;
use hipanel\modules\finance\models\FakeSale;
use hipanel\modules\finance\models\Price;
use hipanel\widgets\AjaxModal;
use hipanel\widgets\IndexPage;
use yii\bootstrap\Dropdown;
use yii\bootstrap\Modal;
use yii\helpers\Html;


/**
 * @var \yii\web\View $this
 * @var \hipanel\modules\finance\models\Plan $model
 * @var \hipanel\modules\finance\models\Sale[] $salesByObject
 * @var \hipanel\modules\finance\models\Price[] $pricesByMainObject
 */

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
                        'client',
                        'type',
                        'state',
                        'note',
                    ]),
                ]) ?>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <?php $page = IndexPage::begin(['model' => $model, 'layout' => 'noSearch']) ?>
        <?php $page->beginContent('show-actions') ?>
            <h4 class="box-title" style="display: inline-block;">&nbsp;<?= Yii::t('hipanel:finance', 'Prices') ?></h4>
        <?php $page->endContent() ?>

        <?php $page->beginContent('main-actions') ?>
            <?= AjaxModal::widget([
                'id' => 'create-prices-modal',
                'header' => Html::tag('h4', Yii::t('hipanel.finance.price', 'Create prices'), ['class' => 'modal-title']),
                'scenario' => 'create-prices',
                'actionUrl' => ['@plan/create-prices', 'id' => $model->id],
                'size' => Modal::SIZE_SMALL,
                'toggleButton' => ['label' => Yii::t('hipanel', 'Create'), 'class' => 'btn btn-sm btn-success'],
            ]) ?>
        <?php $page->endContent() ?>

        <?php $page->beginContent('bulk-actions') ?>
            <?= $page->renderBulkButton(Yii::t('hipanel', 'Update'), Url::to(['@price/update']), 'warning') ?>
            <?= $page->renderBulkButton(Yii::t('hipanel', 'Delete'), Url::to(['@price/delete']), 'danger') ?>
        <?php $page->endContent() ?>

        <?php $page->beginContent('table') ?>
            <?php
            ?>
            <?php $page->beginBulkForm() ?>
                <?= \hipanel\modules\finance\grid\SaleGridView::widget([
                    'boxed' => false,
                    'showHeader' => false,
                    'dataProvider' => (new \yii\data\ArrayDataProvider([
                        'allModels' => $salesByObject,
                        'pagination' => false,
                    ])),
                    'columns' => [
                        'object_link',
                        'seller',
                        'buyer',
                        'time',
                        'price_related_actions',
                    ],
                    'afterRow' => function (\hipanel\modules\finance\models\Sale $sale, $key, $index, $grid) use ($pricesByMainObject) {
                        $prices = $pricesByMainObject[$sale->object_id ?? $sale->tariff_id];
                        if (empty($prices)) {
                            return '';
                        }

                        return \hipanel\modules\finance\grid\PriceGridView::widget([
                            'boxed' => false,
                            'showHeader' => true,
                            'showFooter' => false,
                            'options' => [
                                'tag' => 'tr',
                                'id' => crc32($sale->id ?? microtime(true)),
                            ],
                            'layout' => '<td colspan="5">{items}</td>',
                            'emptyText' => Yii::t('hipanel.finance.price', 'No prices found'),
                            'dataProvider' => (new \yii\data\ArrayDataProvider([
                                'allModels' => $prices,
                                'pagination' => false,
                            ])),
                            'columns' => [
                                'checkbox',
                                'object->name',
                                'object->label',
                                'price/unit',
                                'type',
                                'note',
                            ],
                        ]);
                    }
                ]) ?>
            <?php $page->endBulkForm() ?>
        <?php $page->endContent() ?>
        <?php $page->end() ?>
    </div>
</div>
