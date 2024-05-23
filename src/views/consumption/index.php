<?php

use hipanel\models\IndexPageUiOptions;
use hipanel\modules\finance\grid\ChargeRepresentations;
use hipanel\modules\finance\grid\ConsumptionGridView;
use hipanel\modules\finance\helpers\ConsumptionConfigurator;
use hipanel\modules\finance\models\Consumption;
use hipanel\modules\finance\models\ConsumptionSearch;
use hipanel\widgets\IndexPage;
use hiqdev\hiart\ActiveDataProvider;
use yii\db\ActiveRecordInterface;

/** @var ActiveRecordInterface $originalModel */
/** @var ActiveDataProvider $dataProvider */
/** @var IndexPageUiOptions $uiModel */
/** @var Consumption $model */
/** @var ConsumptionSearch $searchModel */
/** @var ConsumptionConfigurator $configurator */
/** @var ChargeRepresentations $representationCollection */

$this->title = Yii::t('hipanel:finance', 'Resource consumption');
$this->params['subtitle'] = $searchModel->getCurrentClassLabel();
$this->params['breadcrumbs'][] = $this->title;

?>

<?php $page = IndexPage::begin(compact('model', 'dataProvider')) ?>

    <?php $page->setSearchFormData() ?>

        <?php $page->beginContent('sorter-actions') ?>
            <?= $page->renderSorter([
                'attributes' => $model->getSortColumns(),
            ]) ?>
        <?php $page->endContent() ?>

    <?php $page->beginContent('table') ?>
        <?php $page->beginBulkForm() ?>
            <?= ConsumptionGridView::widget([
                'boxed' => false,
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => $representationCollection->getByName($uiModel->representation)->getColumns(),
            ]) ?>
        <?php $page->endBulkForm() ?>
    <?php $page->endContent() ?>

<?php IndexPage::end() ?>
