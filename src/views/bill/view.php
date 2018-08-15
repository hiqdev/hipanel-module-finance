<?php

use hipanel\modules\finance\grid\BillGridView;
use hipanel\modules\finance\grid\ChargeGridView;
use hipanel\modules\finance\menus\BillDetailMenu;
use hipanel\widgets\ClientSellerLink;
use hipanel\widgets\IndexPage;
use hipanel\widgets\MainDetails;
use hipanel\widgets\Pjax;

$this->title = sprintf(
    '%s: %s %s %s',
    $model->client,
    $model->sum,
    $model->currency,
    $model->label
) ?: '&nbsp;';
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Payments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

Pjax::begin(Yii::$app->params['pjax']) ?>
<div class="row">

    <div class="col-md-3">
        <?= MainDetails::widget([
            'title' => $model->gtype_label,
            'icon' => 'fa-money',
            'subTitle' => ClientSellerLink::widget(['model' => $model]),
            'menu' => BillDetailMenu::widget(['model' => $model], ['linkTemplate' => '<a href="{url}" {linkOptions}><span class="pull-right">{icon}</span>&nbsp;{label}</a>']),
        ]) ?>
        <?= BillGridView::detailView([
            'model' => $model,
            'columns' => [
                'type_label', 'quantity',
                'sum_editable', 'balance',
            ],
        ]) ?>
        <?= BillGridView::detailView([
            'model' => $model,
            'columns' => [
                'client_id', 'seller_id',
                'time', 'descr',
                'object', 'tariff_link',
            ],
        ]) ?>
    </div>

    <div class="col-md-9">
        <?php $page = IndexPage::begin(['model' => $model, 'layout' => 'noSearch']) ?>
            <?php $page->beginContent('show-actions') ?>
                <h4 class="box-title" style="display: inline-block;">&nbsp;<?= Yii::t('hipanel:finance', 'Detalization') ?></h4>
            <?php $page->endContent() ?>
            <?php $page->beginContent('table') ?>
                <?php $page->beginBulkForm() ?>
                    <?= ChargeGridView::widget([
                        'boxed' => false,
                        'dataProvider' => new \yii\data\ArrayDataProvider([
                            'allModels' => $model->charges,
                            'sort'=> [
                                'defaultOrder' => [
                                    'id' => SORT_DESC,
                                    'time' => SORT_DESC,
                                ],
                                'attributes' => ['id', 'time'],
                            ],
                            'pagination' => [
                                'pageSize' => 50,
                            ],
                        ]),
                        'filterModel' => $model->charges,
                        'tableOptions' => [
                            'class' => 'table table-striped table-bordered'
                        ],
                        'columns' => [
                            'type_label', 'label',
                            'quantity', 'sum', 'time',
                        ],
                    ]) ?>
                <?php $page->endBulkForm() ?>
            <?php $page->endContent() ?>
        <?php $page->end() ?>
    </div>

</div>
<?php Pjax::end() ?>
