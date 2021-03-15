<?php

use hipanel\models\IndexPageUiOptions;
use hipanel\modules\finance\grid\TargetGridView;
use hipanel\modules\finance\models\Target;
use hipanel\widgets\IndexPage;
use hiqdev\higrid\representations\RepresentationCollection;
use yii\data\DataProviderInterface;

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

    <?php $page->beginContent('sorter-actions') ?>
        <?= $page->renderSorter([
            'attributes' => [
                'name',
            ],
        ]) ?>
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
