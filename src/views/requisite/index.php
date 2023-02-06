<?php

use hipanel\models\IndexPageUiOptions;
use hipanel\modules\finance\grid\RequisiteGridView;
use hipanel\modules\finance\models\RequisiteSearch;
use hipanel\modules\finance\widgets\CdbExportModalButton;
use hipanel\modules\server\grid\HubRepresentations;
use hipanel\widgets\IndexPage;
use hiqdev\hiart\ActiveDataProvider;
use yii\helpers\Html;
use hipanel\widgets\AjaxModal;
use yii\bootstrap\Modal;
use yii\web\View;

/**
 * @var View $this
 * @var array $states
 * @var RequisiteSearch $model
 * @var ActiveDataProvider $dataProvider
 * @var HubRepresentations $representationCollection
 * @var IndexPageUiOptions $uiModel
 */

$this->registerCss("
.balance-cell { display: flex; flex-direction: column; height: 100%; text-align: center; }
.balance-cell > span { flex: 1 1 auto; padding: .3em; }
");

$this->title = Yii::t('hipanel:finance', 'Requisites');
$this->params['subtitle'] = array_filter(Yii::$app->request->get($model->formName(), [])) ? Yii::t('hipanel', 'filtered list') : Yii::t('hipanel', 'full list');
$this->params['breadcrumbs'][] = $this->title;

?>

<?php $page = IndexPage::begin(['model' => $model, 'dataProvider' => $dataProvider]) ?>

    <?php $page->setSearchFormData(['uiModel' => $uiModel]) ?>

    <?php $page->beginContent('main-actions') ?>
        <?= Html::a(Yii::t('hipanel', 'Create'), '@requisite/create', ['class' => 'btn btn-sm btn-success']) ?>
        <?= CdbExportModalButton::widget() ?>
    <?php $page->endContent() ?>

    <?php $page->beginContent('sorter-actions') ?>
        <?= $page->renderSorter([
            'attributes' => [
                'email',
                'name',
                'client',
                'seller',
            ],
        ]) ?>
    <?php $page->endContent() ?>
    <?php $page->beginContent('representation-actions') ?>
        <?= $page->renderRepresentations($representationCollection) ?>
    <?php $page->endContent() ?>

    <?php $page->beginContent('bulk-actions') ?>
        <?= AjaxModal::widget([
                'id' => 'bulk-set-serie-modal',
                'bulkPage' => true,
                'header' => Html::tag('h4', Yii::t('hipanel:finance', 'Set serie'), ['class' => 'modal-title']),
                'scenario' => 'bulk-set-serie',
                'actionUrl' => ['@requisite/bulk-set-serie'],
                'size' => Modal::SIZE_LARGE,
                'toggleButton' => ['label' => Yii::t('hipanel:finance', 'Set serie'), 'class' => 'btn btn-sm btn-default'],
        ]) ?>

        <?= AjaxModal::widget([
                'id' => 'bulk-set-templates-modal',
                'bulkPage' => true,
                'header' => Html::tag('h4', Yii::t('hipanel:finance', 'Set templates'), ['class' => 'modal-title']),
                'scenario' => 'bulk-set-templates',
                'actionUrl' => ['@requisite/bulk-set-templates'],
                'size' => Modal::SIZE_LARGE,
                'toggleButton' => ['label' => Yii::t('hipanel:finance', 'Set templates'), 'class' => 'btn btn-sm btn-default'],
        ]) ?>
    <?php $page->endContent() ?>


    <?php $page->beginContent('table') ?>
        <?php $page->beginBulkForm() ?>
            <?= RequisiteGridView::widget([
                'dataProvider' => $dataProvider,
                'boxed' => false,
                'filterModel'  => $model,
                'columns' => $representationCollection->getByName($uiModel->representation)->getColumns(),
            ]) ?>
        <?php $page->endBulkForm() ?>
    <?php $page->endContent() ?>
<?php $page->end() ?>
