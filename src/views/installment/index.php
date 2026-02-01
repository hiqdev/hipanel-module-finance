<?php

use hipanel\models\IndexPageUiOptions;
use hipanel\modules\finance\grid\InstallmentGridView;
use hipanel\modules\finance\grid\InstallmentRepresentations;
use hipanel\modules\finance\models\SInstallmentSearch;
use hipanel\widgets\gridLegend\GridLegend;
use hipanel\widgets\IndexPage;
use yii\bootstrap\Dropdown;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\web\View;

/**
 * @var View $this
 * @var SaleSearch $model
 * @var IndexPageUiOptions $uiModel
 * @var SaleRepresentations $representationCollection
 * @var ActiveDataProvider $dataProvider
 */

$user = Yii::$app->user;
$this->title = Yii::t('hipanel:finance:sale', 'Installment');
$this->params['breadcrumbs'][] = $this->title;
$subtitle = array_filter(Yii::$app->request->get($model->formName(), [])) ? Yii::t('hipanel', 'filtered list') : Yii::t('hipanel', 'full list');

?>

<?php $page = IndexPage::begin(['model' => $model, 'dataProvider' => $dataProvider]) ?>

    <?php $page->beginContent('sorter-actions') ?>
        <?= $page->renderSorter(['attributes' => ['id', 'start', 'finish']]) ?>
    <?php $page->endContent() ?>

    <?php $page->beginContent('representation-actions') ?>
        <?= $page->renderRepresentations($representationCollection) ?>
    <?php $page->endContent() ?>
    <?php $page->beginContent('table') ?>
        <?php $page->beginBulkForm() ?>
            <?= InstallmentGridView::widget([
                'boxed' => false,
                'dataProvider' => $dataProvider,
                'filterModel' => $model,
                'columns' => $representationCollection->getByName($uiModel->representation)->getColumns(),
                /***
                'rowOptions' => static function ($model): array {
                    return GridLegend::create(new SaleGridLegend($model))->gridRowOptions();
                },
                ***/
            ]) ?>
        <?php $page->endBulkForm() ?>
    <?php $page->endContent() ?>
<?php $page->end() ?>


