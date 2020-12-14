<?php

use hipanel\models\IndexPageUiOptions;
use hipanel\modules\finance\grid\ResourceGridView;
use hipanel\modules\finance\helpers\ResourceConfigurator;
use hipanel\widgets\IndexPage;
use yii\base\ViewContextInterface;
use yii\bootstrap\Html;
use yii\data\DataProviderInterface;
use yii\db\ActiveRecordInterface;

/** @var DataProviderInterface $dataProvider */
/** @var ViewContextInterface $originalContext */
/** @var ActiveRecordInterface $originalSearchModel */
/** @var IndexPageUiOptions $uiModel */
/** @var ResourceConfigurator $configurator */

?>

<?php $page = IndexPage::begin([
    'model' => $originalSearchModel,
    'dataProvider' => $dataProvider,
    'originalContext' => $originalContext,
    'searchView' => $configurator->getSearchView(),
]) ?>
    <?php $page->setSearchFormOptions(['action' => '']) ?>
    <?php $page->setSearchFormData(['uiModel' => $uiModel]) ?>
    <?php $page->beginContent('sorter-actions') ?>
        <?= $page->renderSorter([
            'attributes' => ['id']
        ]) ?>
    <?php $page->endContent() ?>
    <?php $page->beginContent('alt-actions') ?>
        <div class="form-group has-feedback">
            <?= Html::textInput('date-range', null, ['class' => 'form-control', 'id' => 'date-range']) ?>
            <?= Html::tag('span', null, ['class' => 'glyphicon glyphicon-calendar form-control-feedback text-muted']) ?>
        </div>
    <?php $page->endContent() ?>
    <?php $page->beginContent('table') ?>
        <?php $page->beginBulkForm() ?>
            <?= $configurator->renderGridView([
                'boxed' => false,
                'dataProvider' => $dataProvider,
                'filterModel' => $originalSearchModel,
                'filterRowOptions' => ['style' => 'display: none;'],
                'columns' => ResourceGridView::getColumns($configurator),
                'showFooter' => true,
                'placeFooterAfterBody' => true,
                'emptyCell' => null,
            ]) ?>
        <?php $page->endBulkForm() ?>
    <?php $page->endContent() ?>
<?php $page::end() ?>
