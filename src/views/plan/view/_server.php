<?php

use hipanel\modules\finance\models\Price;
use hipanel\modules\finance\models\Sale;
use hipanel\widgets\AjaxModal;
use hipanel\widgets\IndexPage;
use yii\bootstrap\Modal;
use yii\helpers\Html;


/**
 * @var \yii\web\View $this
 * @var \hipanel\modules\finance\models\Plan $model
 * @var IndexPage $page
 * @var \hipanel\modules\finance\helpers\PlanInternalsGrouper $grouper
 * @var Sale[] $salesByObject
 * @var Price[][] $pricesByMainObject
 */

[$salesByObject, $pricesByMainObject] = $grouper->group();

?>
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

<?php $page->beginContent('table') ?>
<?php $page->beginBulkForm() ?>
    <?= \hipanel\modules\finance\grid\SalesInPlanGridView::widget([
        'boxed' => false,
        'showHeader' => false,
        'pricesBySoldObject' => $pricesByMainObject,
        'dataProvider' => (new \yii\data\ArrayDataProvider([
            'allModels' => $salesByObject,
            'pagination' => false,
        ])),
        'columns' => [
            'object_link',
            'object_label',
            'seller',
            'buyer',
            'time',
            'price_related_actions',
        ]
    ]) ?>
<?php $page->endBulkForm() ?>
<?php $page->endContent() ?>
