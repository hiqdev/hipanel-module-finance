<?php

use hipanel\models\IndexPageUiOptions;
use hipanel\modules\finance\grid\ChargeGridView;
use hipanel\modules\finance\grid\ChargeRepresentations;
use hipanel\modules\finance\models\ChargeSearch;
use hipanel\widgets\IndexPage;
use yii\data\ActiveDataProvider;
use yii\web\View;

/**
 * @var ActiveDataProvider $dataProvider
 * @var View $this
 * @var ChargeSearch $model
 * @var array $billTypesList
 * @var array $clientTypes
 * @var ChargeRepresentations $representationCollection
 * @var IndexPageUiOptions $uiModel
 */

$this->title = Yii::t('hipanel:finance', 'Charges');
$this->params['subtitle'] = array_filter(Yii::$app->request->get($model->formName(), [])) ? Yii::t('hipanel', 'filtered list') : Yii::t('hipanel', 'full list');
$this->params['breadcrumbs'][] = $this->title;

?>

<?php $page = IndexPage::begin(['model' => $model, 'dataProvider' => $dataProvider]) ?>

    <?php $page->setSearchFormData(
        [
        'clientTypes' => $clientTypes,
        'billTypesList' => $billTypesList,
    ]) ?>

    <?php $page->beginContent('sorter-actions') ?>
        <?= $page->renderSorter([
            'attributes' => [
                'client',
                'seller',
                'tariff',
                'type_label',
                'sum',
                'quantity',
                'time',
            ],
        ]) ?>
    <?php $page->endContent() ?>

    <?php $page->beginContent('representation-actions') ?>
        <?= $page->renderRepresentations($representationCollection) ?>
    <?php $page->endContent() ?>

    <?php $page->beginContent('table') ?>
        <?php $page->beginBulkForm() ?>
            <?= ChargeGridView::widget([
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
                'filterModel' => $model,
                'boxed' => false,
                'columns' => $representationCollection->getByName($uiModel->representation)->getColumns(),
            ]) ?>
        <?php $page->endBulkForm() ?>
    <?php $page->endContent() ?>

<?php $page->end() ?>

