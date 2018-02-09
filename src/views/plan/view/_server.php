<?php

use hipanel\widgets\AjaxModal;
use hipanel\widgets\IndexPage;
use yii\bootstrap\Modal;
use yii\helpers\Html;


/**
 * @var \yii\web\View $this
 * @var \hipanel\modules\finance\models\Plan $model
 * @var IndexPage $page
 * @var \hipanel\modules\finance\helpers\PlanInternalsGrouper $grouper
 * @var \hipanel\modules\finance\models\Sale[] $salesByObject
 * @var \hipanel\modules\finance\models\Price[] $pricesByMainObject
 */

[$salesByObject, $pricesByMainObject] = $grouper->groupServerPrices();

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
                    'price',
                    'type',
                    'note',
                ],
            ]);
        }
    ]) ?>
<?php $page->endBulkForm() ?>
<?php $page->endContent() ?>
