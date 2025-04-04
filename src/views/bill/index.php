<?php

use hipanel\modules\finance\grid\BillGridView;
use hipanel\modules\finance\models\BillSearch;
use hipanel\modules\finance\models\ExchangeRate;
use hipanel\modules\finance\widgets\BillImportDropdownButton;
use hipanel\modules\finance\widgets\CreateBillWithSplitDropdownButton;
use hipanel\modules\finance\widgets\ExchangeRatesLine;
use hipanel\widgets\IndexPage;
use hiqdev\hiart\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\View;
use hipanel\models\IndexPageUiOptions;
use hipanel\modules\finance\grid\BillRepresentations;

/**
 * @var View $this
 * @var array $billTypesList
 * @var array $clientTypes
 * @var ActiveDataProvider $dataProvider
 * @var BillSearch $model
 * @var ExchangeRate[] $rates
 * @var IndexPageUiOptions $uiModel
 * @var BillRepresentations $representationCollection
 */

$this->title = Yii::t('hipanel:finance', 'Bills');
$this->params['breadcrumbs'][] = $this->title;
$subtitle = array_filter(Yii::$app->request->get($model->formName(), [])) ? Yii::t('hipanel', 'filtered list') : Yii::t('hipanel', 'full list');
$this->params['subtitle'] = $subtitle . ' ' . ExchangeRatesLine::widget(['rates' => $rates]);

?>

<?php $page = IndexPage::begin(['model' => $model, 'dataProvider' => $dataProvider]) ?>

    <?php $page->setSearchFormData([
      'billTypesList' => $billTypesList,
      'clientTypes' => $clientTypes,
    ]) ?>

    <?php $page->beginContent('main-actions') ?>
        <?php if (Yii::$app->user->can('deposit')) : ?>
            <?= Html::a(Yii::t('hipanel:finance', 'Recharge account'), ['@pay/deposit'], ['class' => 'btn btn-sm btn-success']) ?>
        <?php endif ?>
        <?php if (Yii::$app->user->can('bill.create')) : ?>
            <?= CreateBillWithSplitDropdownButton::widget() ?>
            <?= Html::a(Yii::t('hipanel:finance', 'Add internal transfer'), ['@bill/create-transfer'], ['class' => 'btn btn-sm btn-default']) ?>
        <?php endif ?>
        <?php if (Yii::$app->user->can('bill.create-exchange')) : ?>
            <?= Html::a(Yii::t('hipanel:finance', 'Currency exchange'), ['@bill/create-exchange'], ['class' => 'btn btn-sm btn-default']) ?>
        <?php endif ?>
        <?= BillImportDropdownButton::widget() ?>
    <?php $page->endContent() ?>

    <?php $page->beginContent('sorter-actions') ?>
        <?= $page->renderSorter([
            'attributes' => array_filter([
                'sum', 'balance',
                'type', 'descr',
                'time', 'no',
                 Yii::$app->user->can('resell') ? 'client' : null,
                 Yii::$app->user->can('access-subclients') ? 'seller' : null
            ]),
        ]) ?>
    <?php $page->endContent() ?>

    <?php $page->beginContent('bulk-actions') ?>
        <?php if (Yii::$app->user->can('bill.create')) : ?>
            <?= $page->renderBulkButton('copy', Yii::t('hipanel', 'Copy')) ?>
            <?= $page->renderBulkButton('generate-invoice', Yii::t('hipanel:finance', 'Generate invoice')) ?>
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
            'billTypeList' => $billTypesList,
            'boxed' => false,
            'resizableColumns' => false,
            'layout' => <<<"HTML"
                <div class='row'>
                    <div class='col-xs-11'>{sorter}</div>
                </div>
                <div class='table-responsive'>{items}</div>
                <div class='row'>
                    <div class='col-xs-12' style="display: flex; flex-direction: row; flex-wrap: wrap; justify-content: space-between;">
                        <div class='dataTables_info'>{summary}</div>
                        <div class='dataTables_paginate paging_bootstrap'>{pager}</div>
                    </div>
                </div>
HTML,
            'dataProvider' => $dataProvider,
            'filterModel'  => $model,
            'currencies' => $this->context->getCurrencyTypes(),
            'columns' => $representationCollection->getByName($uiModel->representation)->getColumns(),
        ]) ?>
    <?php $page->endBulkForm() ?>
    <?php $page->endContent() ?>
<?php $page->end() ?>
