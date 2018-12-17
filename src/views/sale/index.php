<?php

use hipanel\modules\finance\grid\SaleGridView;
use hipanel\widgets\IndexPage;
use hipanel\widgets\Pjax;
use yii\helpers\Html;

/** @var \yii\web\View $this */
/** @var \hipanel\modules\finance\models\SaleSearch $model */
/** @var \hipanel\models\IndexPageUiOptions $uiModel */
/** @var \hipanel\modules\finance\grid\SaleRepresentations $representationCollection*/
/** @var \yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('hipanel:finance:sale', 'Sales');
$this->params['breadcrumbs'][] = $this->title;
$subtitle = array_filter(Yii::$app->request->get($model->formName(), [])) ? Yii::t('hipanel', 'filtered list') : Yii::t('hipanel', 'full list');
$this->registerCss("
    .sale-flex-cnt {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        flex-wrap: wrap;
    }
");

?>

<?php Pjax::begin(array_merge(Yii::$app->params['pjax'], ['enablePushState' => true])) ?>
    <?php $page = IndexPage::begin(compact('model', 'dataProvider')) ?>

        <?php $page->setSearchFormData([]) ?>

        <?php $page->beginContent('sorter-actions') ?>
            <?= $page->renderSorter(['attributes' => ['id', 'time']]) ?>
        <?php $page->endContent() ?>

        <?php $page->beginContent('bulk-actions') ?>
            <?php if (Yii::$app->user->can('sale.delete')) : ?>
                <?= $page->renderBulkDeleteButton('@sale/delete') ?>
            <?php endif ?>
        <?php $page->endContent() ?>


        <?php $page->beginContent('table') ?>
            <?php $page->beginBulkForm() ?>
                <?= SaleGridView::widget([
                    'boxed' => false,
                    'dataProvider' => $dataProvider,
                    'filterModel' => $model,
                    'columns' => $representationCollection->getByName($uiModel->representation)->getColumns(),
                ]) ?>
            <?php $page->endBulkForm() ?>
        <?php $page->endContent() ?>
    <?php $page->end() ?>
<?php Pjax::end() ?>
