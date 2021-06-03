<?php

use hipanel\modules\finance\grid\PurseGridView;
use hipanel\widgets\IndexPage;
use hiqdev\hiart\ActiveDataProvider;
use hiqdev\hiart\ActiveRecord;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var array $billTypes
 * @var array $billGroupLabels
 * @var ActiveDataProvider $dataProvider
 * @var ActiveRecord $model
 * @var ExchangeRate[] $rates
 */
$this->title = Yii::t('hipanel:finance', 'Purses');
$this->params['breadcrumbs'][] = $this->title;
$subtitle = array_filter(Yii::$app->request->get($model->formName(), [])) ? Yii::t('hipanel', 'filtered list') : Yii::t('hipanel', 'full list');

?>

<?php $page = IndexPage::begin(compact('model', 'dataProvider')) ?>

    <?php $page->setSearchFormData(compact('billTypes', 'billGroupLabels')) ?>

    <?php $page->beginContent('main-actions') ?>
        <?php if (Yii::$app->user->can('purse.update')) : ?>
            <?= Html::a(Yii::t('hipanel:finance', 'Add purse'), ['@purse/create'], ['class' => 'btn btn-sm btn-success']) ?>
        <?php endif ?>
<?php $page->endContent() ?>

    <?php $page->beginContent('sorter-actions') ?>
        <?= $page->renderSorter([
            'attributes' => array_filter([
                'balance',
                'currency_id',
                'requisite_id',
                'contact_id',
                'id',
                 Yii::$app->user->can('resell') ? 'client' : null,
                 Yii::$app->user->can('support') ? 'seller' : null
            ]),
        ]) ?>
    <?php $page->endContent() ?>

    <?php $page->beginContent('bulk-actions') ?>
        <?php if (Yii::$app->user->can('purse.update')) : ?>
            <?= $page->renderBulkButton('@purse/update', Yii::t('hipanel', 'Update')) ?>
        <?php endif ?>
        <?php if (Yii::$app->user->can('purse.delete')) : ?>
            <?= $page->renderBulkDeleteButton('delete') ?>
        <?php endif ?>
    <?php $page->endContent() ?>

    <?php $page->beginContent('table') ?>
    <?php $page->beginBulkForm() ?>
        <?= PurseGridView::widget([
            'boxed' => false,
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
            'columns' => $representationCollection->getByName($uiModel->representation)->getColumns(),
        ]) ?>
    <?php $page->endBulkForm() ?>
    <?php $page->endContent() ?>
<?php $page->end() ?>
