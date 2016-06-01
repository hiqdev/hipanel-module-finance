<?php

use hipanel\modules\finance\grid\BillGridView;
use hipanel\widgets\ActionBox;
use hipanel\widgets\IndexLayoutSwitcher;
use hipanel\widgets\IndexPage;
use hipanel\widgets\Pjax;
use yii\helpers\Html;

$this->title                   = Yii::t('app', 'Bills');
$this->params['breadcrumbs'][] = $this->title;
$this->subtitle = array_filter(Yii::$app->request->get($model->formName(), [])) ? Yii::t('hipanel', 'filtered list') : Yii::t('hipanel', 'full list'); ?>

<?php Pjax::begin(array_merge(Yii::$app->params['pjax'], ['enablePushState' => true])) ?>
    <?php $page = IndexPage::begin(compact('model', 'dataProvider')) ?>

        <?= $page->setSearchFormData(compact('type')) ?>

        <?php $page->beginContent('main-actions') ?>
            <?php if (Yii::$app->user->can('manage')) : ?>
                <?= Html::a(Yii::t('hipanel/finance', 'Add payment'), 'create', ['class' => 'btn btn-sm btn-success']) ?>
            <?php endif; ?>
            <?= Html::a(Yii::t('hipanel/finance', 'Recharge account'), ['@pay/deposit'], ['class' => 'btn btn-sm btn-success']) ?>
        <?php $page->endContent() ?>

        <?php $page->beginContent('show-actions') ?>
            <?= IndexLayoutSwitcher::widget() ?>
            <?= $page->renderSorter([
                'attributes' => [
                    'seller',
                    'client',
                    'sum',
                    'balance',
                    'type',
                    'descr',
                ],
            ]) ?>
            <?= $page->renderPerPage() ?>
        <?php $page->endContent() ?>

        <?php $page->beginContent('bulk-actions') ?>
            <?php
            // TODO: implement bills edit
//            if (Yii::$app->user->can('manage')) print $page->renderBulkButton(Yii::t('app', 'Edit'), 'edit');
            ?>
            <?php if (Yii::$app->user->can('delete-bills')) print $page->renderBulkButton(Yii::t('hipanel', 'Delete'), 'delete', 'danger'); ?>
        <?php $page->endContent() ?>

        <?php $page->beginContent('table') ?>
        <?php $page->beginBulkForm() ?>
            <?= BillGridView::widget([
                'boxed' => false,
                'dataProvider' => $dataProvider,
                'filterModel'  => $model,
                'columns'      => [
                    'checkbox', 'client_id', 'time', 'sum', 'balance',
                    'type_label', 'description',
                ],
            ]) ?>
        <?php $page->endBulkForm() ?>
        <?php $page->endContent() ?>
    <?php $page->end() ?>
<?php Pjax::end() ?>
