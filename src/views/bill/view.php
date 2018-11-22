<?php

use hipanel\modules\finance\models\Charge;
use hipanel\modules\finance\helpers\ChargesGrouper;
use hipanel\modules\finance\grid\BillGridView;
use hipanel\modules\finance\menus\BillDetailMenu;
use hipanel\widgets\ClientSellerLink;
use hipanel\widgets\IndexPage;
use hipanel\widgets\MainDetails;
use hipanel\widgets\Pjax;

/**
 * @var \yii\web\View $this
 * @var \hipanel\modules\finance\models\Charge $model
 * @var IndexPage $page
 * @var \hipanel\modules\finance\helpers\ChargesGrouper $grouper
 * @var Charge[] $idToNameObject
 * @var Charge[][] $chargesByMainObject
 */


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
            <?= $this->render('_grouping', compact('model', 'grouper', 'page')) ?>
        <?php $page->end() ?>
    </div>

</div>
<?php Pjax::end() ?>
