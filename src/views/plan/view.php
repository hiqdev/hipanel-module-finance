<?php

use hipanel\helpers\Url;
use hipanel\modules\finance\grid\PlanGridView;
use hipanel\modules\finance\grid\PriceGridView;
use hipanel\modules\finance\menus\PlanDetailMenu;
use hipanel\modules\finance\models\Price;
use hipanel\modules\finance\models\PriceSearch;
use hipanel\widgets\IndexPage;
use yii\helpers\Html;

$this->title = Html::encode($model->name);
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel.finance.plan', 'Plans'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss("
    .profile-block {
        text-align: center;
    }
");

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
                    <span class="profile-user-name">test text</span>
                </p>

                <div class="profile-usermenu">
                    <?= PlanDetailMenu::widget(['model' => $model]) ?>
                </div>

            </div>
            <div class="box-footer no-padding">
                <?= PlanGridView::detailView([
                    'model' => $model,
                    'boxed' => false,
//                    'gridOptions' => [
//                        'typeOptions' => $typeOptions,
//                    ],
                    'columns' => array_filter([
                        'name',
                    ]),
                ]) ?>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <?php $page = IndexPage::begin(['model' => $model, 'layout' => 'noSearch']) ?>
        <?php $page->beginContent('show-actions') ?>
            <h4 class="box-title" style="display: inline-block;">&nbsp;<?= Yii::t('hipanel.finance.price', 'Prices') ?></h4>
        <?php $page->endContent() ?>

        <?php $page->beginContent('bulk-actions') ?>
            <?= $page->renderBulkButton(Yii::t('hipanel', 'Delete'), Url::to(['/finance/price/delete']), 'danger') ?>
        <?php $page->endContent() ?>

        <?php $page->beginContent('table') ?>
            <?php $page->beginBulkForm() ?>
                <?= PriceGridView::widget([
                    'boxed' => false,
                    'dataProvider' => (new \yii\data\ArrayDataProvider([
                        'allModels' => $model->prices,
                        'pagination' => [
                            'pageSize' => 10,
                        ],
                    ])),
                    'columns' => [
                        'checkbox',
                        'price',
                        'currency',
                        'plan',
                        'unit',
                        'type',
                    ],
                ]) ?>
            <?php $page->endBulkForm() ?>
        <?php $page->endContent() ?>
        <?php $page->end() ?>
    </div>
</div>
