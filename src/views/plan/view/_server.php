<?php

use hipanel\helpers\Url;
use hipanel\modules\finance\grid\PlanGridView;
use hipanel\modules\finance\menus\PlanDetailMenu;
use hipanel\modules\finance\models\Price;
use hipanel\widgets\IndexPage;


/**
 * @var \yii\web\View $this
 * @var \hipanel\modules\finance\models\Plan $model
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

        <?php $page->beginContent('bulk-actions') ?>
            <?= $page->renderBulkButton(Yii::t('hipanel', 'Delete'), Url::to(['/finance/price/delete']), 'danger') ?>
        <?php $page->endContent() ?>

        <?php $page->beginContent('table') ?>
            <?php $page->beginBulkForm() ?>
                <?= \hipanel\modules\finance\grid\SaleGridView::widget([
                    'boxed' => false,
                    'dataProvider' => (new \yii\data\ArrayDataProvider([
                        'allModels' => array_merge(
                            [
                                new \hipanel\modules\finance\models\FakeSale([
                                    'object' => Yii::t('hipanel.finance.price', 'Applicable for all objects'),
                                ])
                            ],
                            $model->sales
                        ),
                        'pagination' => false,
                    ])),
                    'columns' => [
                        'object_link',
                        'seller',
                        'buyer',
                        'time',
                        'price_related_actions',
                    ],
                    'afterRow' => function (\hipanel\modules\finance\models\Sale $sale, $key, $index, $grid) use ($model) {
                        return \hipanel\modules\finance\grid\PriceGridView::widget([
                            'boxed' => false,
                            'showHeader' => true,
                            'showFooter' => false,
                            'layout' => '<tr><td colspan="5">{items}</td></tr>',
                            'emptyText' => Yii::t('hipanel.finance.price', 'No prices found'),
                            'dataProvider' => (new \yii\data\ArrayDataProvider([
                                'allModels' => array_filter($model->prices, function (Price $price) use ($sale) {
                                    return $price->main_object_id === $sale->object_id;
                                }),
                                'pagination' => false,
                            ])),
                            'columns' => [
                                'checkbox',
                                'object->type',
                                'object->name',
                                'price/unit',
                                'type',
                            ],
                        ]);
                    }
                ]) ?>
            <?php $page->endBulkForm() ?>
        <?php $page->endContent() ?>
        <?php $page->end() ?>
    </div>
</div>
