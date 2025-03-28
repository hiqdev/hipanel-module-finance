<?php

use yii\helpers\Html;
use hipanel\widgets\IndexPage;
use yii\data\DataProviderInterface;
use hipanel\models\IndexPageUiOptions;
use hipanel\modules\finance\models\Target;
use hipanel\modules\finance\grid\TargetGridView;
use hiqdev\higrid\representations\RepresentationCollection;

/** @var Target $model */
/** @var DataProviderInterface $dataProvider */
/** @var RepresentationCollection $representationCollection */
/** @var IndexPageUiOptions $uiModel */

$this->title = Yii::t('hipanel:finance', 'Targets');
$this->params['subtitle'] = array_filter(Yii::$app->request->get($model->formName(), [])) ? Yii::t('hipanel', 'filtered list') : Yii::t('hipanel', 'full list');
$this->params['breadcrumbs'][] = $this->title;

?>


<?php $page = IndexPage::begin(compact('model', 'dataProvider')) ?>

    <?php $page->setSearchFormData() ?>

    <?php $page->beginContent('main-actions') ?>
        <?php if (Yii::$app->user->can('plan.create')) : ?>
            <?= Html::a(Yii::t('hipanel:finance', 'Create'), ['@target/create'], ['class' => 'btn btn-sm btn-success']) ?>
        <?php endif ?>
    <?php $page->endContent() ?>

    <?php $page->beginContent('sorter-actions') ?>
        <?= $page->renderSorter([
            'attributes' => [
                'name',
            ],
        ]) ?>
    <?php $page->endContent() ?>

    <?php $page->beginContent('bulk-actions') ?>
        <?php if (Yii::$app->user->can('test.alpha')) : ?>
            <?= $page->renderBulkButton('restore', Yii::t('hipanel', 'Restore')) ?>
        <?php endif ?>
    <?php $page->endContent() ?>

    <?php $page->beginContent('representation-actions') ?>
        <?= $page->renderRepresentations($representationCollection) ?>
    <?php $page->endContent() ?>

    <?php $page->beginContent('table') ?>
        <?php $page->beginBulkForm() ?>
            <?= TargetGridView::widget([
                'dataProvider' => $dataProvider,
                'boxed' => false,
                'filterModel'  => $model,
                'columns' => $representationCollection->getByName($uiModel->representation)->getColumns(),
            ]) ?>
            <?php $page->endBulkForm() ?>
    <?php $page->endContent() ?>
<?php IndexPage::end() ?>
