<?php

use hipanel\modules\finance\grid\BillGridView;
use hipanel\modules\finance\helpers\ChargesGrouper;
use hipanel\modules\finance\menus\BillDetailMenu;
use hipanel\modules\finance\models\Charge;
use hipanel\widgets\ClientSellerLink;
use hipanel\widgets\IndexPage;
use hipanel\widgets\MainDetails;
use yii\web\View;

/**
 * @var array $currencies
 * @var View $this
 * @var Charge $model
 * @var IndexPage $page
 * @var ChargesGrouper $grouper
 * @var Charge[] $idToNameObject
 * @var Charge[][] $chargesByMainObject
 */

$this->title = $model->pageTitle;
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Payments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$detalizationAllowed = Yii::$app->params['module.finance.bill.detalization.allowed'] || Yii::$app->user->can('bill.charges.read');

$this->registerCss(
    <<<CSS
.affix-wrapper {
    display: flex;
    flex-direction: row;
    justify-content: end;
    position: relative;
    overflow: hidden;
} 
.affix {
    top: .5em;
    z-index: 500;
    background-color: #fff;
}
CSS
);
$this->registerJs(
    <<<'JS'
const $el = $(".box-bulk-actions");
$el.affix({
  offset: {
    top: $el.offset().top,
    bottom: function () {
      return (this.bottom = $(".main-footer").outerHeight(true));
    },
  },
});
JS
);
?>

<div class="row">

    <div class="col-md-3">
        <?= MainDetails::widget([
            'title' => Yii::t('hipanel:finance', $model->gtype_label),
            'icon' => 'fa-money',
            'subTitle' => ClientSellerLink::widget(['model' => $model]),
            'menu' => BillDetailMenu::widget(
                ['model' => $model],
                ['linkTemplate' => '<a href="{url}" {linkOptions}><span class="pull-right">{icon}</span>&nbsp;{label}</a>']
            ),
        ]) ?>
        <?= BillGridView::detailView([
            'model' => $model,
            'boxOptions' => ['bodyOptions' => ['class' => 'no-padding']],
            'columns' => [
                'type_label',
                'quantity',
                'sum_editable',
                'balance',
                'payment_status',
            ],
        ]) ?>
        <?= BillGridView::detailView([
            'model' => $model,
            'boxOptions' => ['bodyOptions' => ['class' => 'no-padding table-responsive']],
            'columns' => [
                'client_id',
                'seller_id',
                'time',
                'description',
                'object',
                'tariff_link',
                'requisite',
            ],
        ]) ?>
    </div>

    <?php if ($detalizationAllowed || Yii::$app->user->can('bill.charges.read')): ?>
        <div class="col-md-9">
            <?php $page = IndexPage::begin(['model' => $model, 'layout' => 'noSearch']) ?>
            <?php $page->beginContent('show-actions') ?>
            <h4 class="box-title" style="display: inline-block;">&nbsp;<?= Yii::t('hipanel:finance', 'Detalization') ?></h4>
            <?php $page->endContent() ?>
            <?php $page->beginContent('bulk-actions') ?>
            <?php if (Yii::$app->user->can('bill.update')) : ?>
                <?= $page->renderBulkButton(['@charge/update', 'bill_id' => $model->id], Yii::t('hipanel', 'Update'), ['color' => 'success']) ?>
                <?= $page->renderBulkDeleteButton('charge-delete') ?>
            <?php endif ?>
            <?php $page->endContent() ?>
            <?= $this->render('_grouping', [
                'model' => $model,
                'grouper' => $grouper,
                'page' => $page,
                'currencies' => $currencies,
            ]) ?>
            <?php IndexPage::end() ?>
        </div>
    <?php endif; ?>
</div>

<?php $this->registerJs(
    <<<JS
        let chargeId = getAnchor();
        $('tr [data-key="' + chargeId + '"]').css('background-color', 'lightblue').css('border', '1pt solid #8cb7c5');

        function getAnchor() {
            var currentUrl = document.URL,
            urlParts   = currentUrl.split('#');

            return (urlParts.length > 1) ? urlParts[1] : null;
        }
JS
) ?>
