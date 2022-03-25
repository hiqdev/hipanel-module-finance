<?php

use hipanel\modules\finance\grid\TariffProfileGridView;
use hipanel\widgets\IndexPage;
use hipanel\widgets\Pjax;
use yii\helpers\Html;

/**
 * @var \yii\web\View
 * @var array $types
 */
$this->title = Yii::t('hipanel.finance.tariffprofile', 'Tariff profiles');
$this->params['subtitle'] = array_filter(Yii::$app->request->get($model->formName(), [])) ? Yii::t('hipanel', 'filtered list') : Yii::t('hipanel', 'full list');
$this->params['breadcrumbs'][] = $this->title;

?>

<?php $page = IndexPage::begin(compact('model', 'dataProvider')) ?>

        <?php $page->beginContent('main-actions') ?>
            <?= Html::a(Yii::t('hipanel.finance.tariffprofile', 'Create profile'), ['@tariffprofile/create'], ['class' => 'btn btn-sm btn-success']) ?>
        <?php $page->endContent() ?>

    <?php $page->beginContent('sorter-actions') ?>
    <?php $page->endContent() ?>

    <?php $page->beginContent('bulk-actions') ?>
        <?php if (Yii::$app->user->can('plan.delete')) : ?>
            <?= $page->renderBulkDeleteButton('delete') ?>
        <?php endif ?>
    <?php $page->endContent() ?>

    <?php $page->beginContent('table') ?>
        <?php $page->beginBulkForm() ?>
        <?= TariffProfileGridView::widget([
            'boxed' => false,
            'dataProvider' => $dataProvider,
            'filterModel' => $model,
            'columns' => [
                'checkbox',
                'name',
                'client',
                'tariff_names',
                'actions',
            ],
        ]) ?>
        <?php $page->endBulkForm() ?>
    <?php $page->endContent() ?>
<?php $page->end() ?>
