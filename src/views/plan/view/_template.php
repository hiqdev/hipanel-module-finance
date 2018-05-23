<?php

use hipanel\widgets\AjaxModal;
use hipanel\widgets\IndexPage;
use yii\bootstrap\Modal;
use yii\helpers\Html;

/**
 * @var \yii\web\View $this
 * @var \hipanel\modules\finance\models\Plan $model
 * @var \hipanel\modules\finance\models\Sale[] $salesByObject
 * @var \hipanel\modules\finance\models\Price[] $pricesByMainObject
 * @var IndexPage $page
 */

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
        <?= \hipanel\modules\finance\grid\PriceGridView::widget([
            'boxed' => false,
            'emptyText' => Yii::t('hipanel.finance.price', 'No prices found'),
            'dataProvider' => (new \yii\data\ArrayDataProvider([
                'allModels' => $model->prices,
                'pagination' => false,
            ])),
            'columns' => [
                'checkbox',
                'object->name',
                'object->label',
                'type',
                'price',
                'note',
            ],
        ]) ?>
    <?php $page->endBulkForm() ?>
<?php $page->endContent() ?>
