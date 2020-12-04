<?php

use hipanel\modules\finance\grid\BillGridView;
use hipanel\modules\finance\widgets\ExchangeRatesLine;
use hipanel\widgets\IndexPage;
use hipanel\widgets\Pjax;
use yii\helpers\Html;

/**
 * @var \yii\web\View $this
 * @var array $billTypes
 * @var array $billGroupLabels
 * @var \hiqdev\hiart\ActiveDataProvider $dataProvider
 * @var \hiqdev\hiart\ActiveRecord $model
 * @var \hipanel\modules\finance\models\ExchangeRate[] $rates
 */
$this->title = Yii::t('hipanel:finance', 'Bills');
$this->params['breadcrumbs'][] = $this->title;
$subtitle = array_filter(Yii::$app->request->get($model->formName(), [])) ? Yii::t('hipanel', 'filtered list') : Yii::t('hipanel', 'full list');
$this->params['subtitle'] = $subtitle . ' ' . ExchangeRatesLine::widget(['rates' => $rates]);

?>

<?php Pjax::begin(array_merge(Yii::$app->params['pjax'], ['enablePushState' => true])) ?>
    <?php $page = IndexPage::begin(compact('model', 'dataProvider')) ?>

        <?php $page->setSearchFormData(compact('billTypes', 'billGroupLabels')) ?>

        <?php $page->beginContent('main-actions') ?>
            <?php if (Yii::$app->user->can('deposit')) : ?>
                <?= Html::a(Yii::t('hipanel:finance', 'Recharge account'), ['@pay/deposit'], ['class' => 'btn btn-sm btn-success']) ?>
            <?php endif ?>
            <?php if (Yii::$app->user->can('bill.create')) : ?>
                <?= Html::a(Yii::t('hipanel:finance', 'Add payment'), ['@bill/create'], ['class' => 'btn btn-sm btn-success']) ?>
                <?= Html::a(Yii::t('hipanel:finance', 'Add internal transfer'), ['@bill/create-transfer'], ['class' => 'btn btn-sm btn-default']) ?>
            <?php endif ?>
            <?= Html::a(Yii::t('hipanel:finance', 'Currency exchange'), ['@bill/create-exchange'], ['class' => 'btn btn-sm btn-default']) ?>
            <?php if (Yii::$app->user->can('bill.import')) : ?>
                <?= Html::a(Yii::t('hipanel:finance', 'Import payments'), ['@bill/import'], ['class' => 'btn btn-sm btn-default']) ?>
            <?php endif ?>
        <?php $page->endContent() ?>

        <?php $page->beginContent('sorter-actions') ?>
            <?= $page->renderSorter([
                'attributes' => array_filter([
                    'sum', 'balance',
                    'type', 'descr',
                    'time', 'no',
                     Yii::$app->user->can('resell') ? 'client' : null,
                     Yii::$app->user->can('support') ? 'seller' : null
                ]),
            ]) ?>
        <?php $page->endContent() ?>

        <?php $page->beginContent('bulk-actions') ?>
            <?php if (Yii::$app->user->can('bill.create')) : ?>
                <?= $page->renderBulkButton('copy', Yii::t('hipanel', 'Copy')) ?>
            <?php endif ?>
            <?php if (Yii::$app->user->can('bill.update')) : ?>
                <?= $page->renderBulkButton('@bill/update', Yii::t('hipanel', 'Update')) ?>
            <?php endif ?>
            <?php if (Yii::$app->user->can('bill.delete')) : ?>
                <?= $page->renderBulkDeleteButton('delete') ?>
            <?php endif ?>
        <?php $page->endContent() ?>

        <?php $page->beginContent('table') ?>
        <?php $page->beginBulkForm() ?>
            <?= BillGridView::widget([
                'boxed' => false,
                'layout' => "<div class='row'><div class='col-xs-12'>{sorter}</div></div><div class='table-responsive'>{items}</div>\n<div class='row'><div class='col-sm-6 col-sm-offset-6 col-xs-12'><div class='dataTables_paginate paging_bootstrap'>{pager}</div></div></div>\n<div class='row'><div class='col-md-12'><div class='dataTables_info'>{summary}</div></div></div>",
                'dataProvider' => $dataProvider,
                'filterModel'  => $model,
                'currencies' => $this->context->getCurrencyTypes(),
                'columns' => $representationCollection->getByName($uiModel->representation)->getColumns(),
            ]) ?>
        <?php $page->endBulkForm() ?>
        <?php $page->endContent() ?>
    <?php $page->end() ?>
<?php Pjax::end() ?>
