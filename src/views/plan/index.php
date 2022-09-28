<?php

use hipanel\modules\finance\grid\PlanGridView;
use hipanel\modules\finance\models\PlanSearch;
use hipanel\widgets\IndexPage;
use hiqdev\hiart\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var array $states
 * @var PlanSearch $model
 * @var ActiveDataProvider $dataProvider
 */

$this->title = Yii::t('hipanel:finance', 'Tariff plans');
$this->params['breadcrumbs'][] = $this->title;

?>

<?php $page = IndexPage::begin(['model' => $model, 'dataProvider' => $dataProvider]) ?>

    <?php $page->setSearchFormData([]) ?>

    <?php $page->beginContent('main-actions') ?>
        <?php if (Yii::$app->user->can('plan.create')) : ?>
            <?= Html::a(Yii::t('hipanel', 'Create'), ['/finance/plan/create'], ['class' => 'btn btn-sm btn-success']) ?>
        <?php endif ?>
    <?php $page->endContent() ?>

    <?php $page->beginContent('sorter-actions') ?>
        <?= $page->renderSorter(['attributes' => ['id']]) ?>
    <?php $page->endContent() ?>

    <?php $page->beginContent('bulk-actions') ?>
        <?php if (Yii::$app->user->can('plan.create')) : ?>
            <?= $page->renderBulkButton('@plan/restore', Yii::t('hipanel.finance.plan', 'Restore')) ?>
        <?php endif ?>
        <?php if (Yii::$app->user->can('plan.update')) : ?>
            <?= $page->renderBulkDeleteButton('@plan/delete') ?>
        <?php endif ?>
    <?php $page->endContent() ?>

    <?php $page->beginContent('table') ?>
        <?php $page->beginBulkForm() ?>
            <?= PlanGridView::widget([
                'boxed' => false,
                'dataProvider' => $dataProvider,
                'filterModel' => $model,
                'columns' => [
                    'checkbox',
                    'actions',
                    'name',
                    'client',
                    'type',
                    'state',
                    'custom_attributes',
                ],
            ]) ?>
        <?php $page->endBulkForm() ?>
    <?php $page->endContent() ?>
<?php $page->end() ?>
