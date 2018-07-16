<?php

use hipanel\widgets\AjaxModal;
use hipanel\widgets\IndexPage;
use yii\bootstrap\Modal;
use yii\helpers\Html;

/**
 * @var \yii\web\View $this
 * @var \hipanel\modules\finance\models\Plan $model
 * @var \hipanel\modules\finance\helpers\PlanInternalsGrouper $grouper
 * @var IndexPage $page
 */

$prices = $grouper->group();

?>

<?php $page->beginContent('main-actions') ?>
    <?= AjaxModal::widget([
        'id' => 'create-prices-modal',
        'header' => Html::tag('h4', Yii::t('hipanel.finance.price', 'Create prices'), ['class' => 'modal-title']),
        'scenario' => 'create-prices',
        'actionUrl' => ['@plan/suggest-prices-modal', 'id' => $model->id],
        'size' => Modal::SIZE_SMALL,
        'toggleButton' => ['label' => Yii::t('hipanel', 'Create'), 'class' => 'btn btn-sm btn-success'],
    ]) ?>
    <?= Html::a(Yii::t('hipanel', 'Update'), ['@plan/update-prices', 'id' => $model->id], ['class' => 'btn btn-sm btn-warning']) ?>
<?php $page->endContent() ?>

<?php $page->beginContent('table') ?>
    <?php $page->beginBulkForm() ?>
        <?= \hipanel\modules\finance\grid\CertificatePriceGridView::widget([
            'boxed' => false,
            'emptyText' => Yii::t('hipanel.finance.price', 'No prices found'),
            'dataProvider' => (new \yii\data\ArrayDataProvider([
                'allModels' => $prices,
                'pagination' => false,
            ])),
            'filterModel' => $model,
            'columns' => [
                'certificate',
                'purchase',
                'renewal',
            ],
        ]) ?>
    <?php $page->endBulkForm() ?>
<?php $page->endContent() ?>
