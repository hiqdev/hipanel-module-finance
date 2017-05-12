<?php

use hipanel\modules\finance\grid\SaleGridView;
use hipanel\widgets\IndexPage;
use hipanel\widgets\Pjax;
use yii\helpers\Html;

$this->title = Yii::t('hipanel:finance:sale', 'Sale');
$this->params['breadcrumbs'][] = $this->title;
$subtitle = array_filter(Yii::$app->request->get($model->formName(), [])) ? Yii::t('hipanel', 'filtered list') : Yii::t('hipanel', 'full list');

?>

<?php Pjax::begin(array_merge(Yii::$app->params['pjax'], ['enablePushState' => true])) ?>
    <?php $page = IndexPage::begin(compact('model', 'dataProvider')) ?>

        <?php $page->setSearchFormData([]) ?>

        <?php $page->beginContent('main-actions') ?>
        <?php if (Yii::$app->user->can('deposit')) : ?>
            <?= Html::a(Yii::t('hipanel:finance', 'Recharge account'), ['@pay/deposit'], ['class' => 'btn btn-sm btn-success']) ?>
        <?php endif ?>
        <?php if (Yii::$app->user->can('bill.create')) : ?>
            <?= Html::a(Yii::t('hipanel:finance', 'Add payment'), ['@bill/create'], ['class' => 'btn btn-sm btn-success']) ?>
            <?= Html::a(Yii::t('hipanel:finance', 'Currency exchange'), ['@bill/create-exchange'], ['class' => 'btn btn-sm btn-default']) ?>
            <?= Html::a(Yii::t('hipanel:finance', 'Import payments'), ['@bill/import'], ['class' => 'btn btn-sm btn-default']) ?>
        <?php endif ?>
        <?php $page->endContent() ?>

        <?php $page->beginContent('show-actions') ?>
        <?= $page->renderLayoutSwitcher() ?>
        <?= $page->renderSorter(['attributes' => ['id', 'time']]) ?>
        <?= $page->renderPerPage() ?>
        <?php $page->endContent() ?>

        <?php $page->beginContent('bulk-actions') ?>
        <?php if (Yii::$app->user->can('bill.create')) : ?>
            <?= $page->renderBulkButton(Yii::t('hipanel', 'Copy'), 'copy') ?>
        <?php endif ?>
        <?php if (Yii::$app->user->can('bill.update')) : ?>
            <?= $page->renderBulkButton(Yii::t('hipanel', 'Update'), 'update') ?>
        <?php endif ?>
        <?php if (Yii::$app->user->can('bill.delete')) : ?>
            <?= $page->renderBulkButton(Yii::t('hipanel', 'Delete'), 'delete', 'danger') ?>
        <?php endif ?>
        <?php $page->endContent() ?>

        <?php $page->beginContent('table') ?>
        <?php $page->beginBulkForm() ?>
        <?= SaleGridView::widget([
            'boxed' => false,
            'dataProvider' => $dataProvider,
            'filterModel' => $model,
            'columns' => [
                'checkbox',
                'object',
                'seller',
                'buyer',
                'tariff',
                'time',
            ],
        ]) ?>
        <?php $page->endBulkForm() ?>
        <?php $page->endContent() ?>
    <?php $page->end() ?>
<?php Pjax::end() ?>
