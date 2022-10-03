<?php

use hipanel\models\IndexPageUiOptions;
use hipanel\modules\finance\grid\SaleGridLegend;
use hipanel\modules\finance\grid\SaleGridView;
use hipanel\modules\finance\grid\SaleRepresentations;
use hipanel\modules\finance\models\SaleSearch;
use hipanel\modules\finance\widgets\ChangeBuyerButton;
use hipanel\widgets\gridLegend\GridLegend;
use hipanel\widgets\IndexPage;
use yii\bootstrap\Dropdown;
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
$this->title = Yii::t('hipanel:finance:sale', 'Sales');
$this->params['breadcrumbs'][] = $this->title;
$subtitle = array_filter(Yii::$app->request->get($model->formName(), [])) ? Yii::t('hipanel', 'filtered list') : Yii::t('hipanel', 'full list');
$this->registerCss('
    .sale-flex-cnt {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        flex-wrap: wrap;
    }
');

?>

<?php $page = IndexPage::begin(['model' => $model, 'dataProvider' => $dataProvider]) ?>

    <?php $page->setSearchFormData([]) ?>

    <?php $page->beginContent('sorter-actions') ?>
        <?= $page->renderSorter(['attributes' => ['id', 'time', 'unsale_time']]) ?>
    <?php $page->endContent() ?>

    <?php $page->beginContent('representation-actions') ?>
        <?= $page->renderRepresentations($representationCollection) ?>
    <?php $page->endContent() ?>

    <?php $page->beginContent('bulk-actions') ?>
        <?php if (Yii::$app->user->can('sale.update')) : ?>
            <?= $page->renderBulkButton('@sale/update', Yii::t('hipanel', 'Edit')) ?>

            <div class="dropdown" style="display: inline-block">
                <button type="button" class="btn btn-sm btn-default dropdown-toggle"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= Yii::t('hipanel:finance', 'Change buyer') ?>
                    <span class="caret"></span>
                </button>
                <?= Dropdown::widget([
                    'encodeLabels' => false,
                    'options' => ['class' => 'pull-right'],
                    'items' => [
                        ChangeBuyerButton::widget([
                            'scenario' => 'change-buyer-by-one',
                            'url' => ['@sale/change-buyer-by-one'],
                            'modalName' => 'change-buyer-by-one-from-modal-',
                            'modalTitle' => 'Change buyer by one',
                        ]),
                        ChangeBuyerButton::widget(),
                    ],
                ]) ?>
            </div>

        <?php endif ?>
        <?php if (Yii::$app->user->can('sale.delete')) : ?>
            <?= $page->renderBulkDeleteButton('@sale/delete') ?>
        <?php endif ?>
    <?php $page->endContent() ?>

    <?php $page->beginContent('legend') ?>
        <?= GridLegend::widget(['legendItem' => new SaleGridLegend($model)]) ?>
    <?php $page->endContent() ?>

    <?php $page->beginContent('table') ?>
        <?php $page->beginBulkForm() ?>
            <?= SaleGridView::widget([
                'boxed' => false,
                'dataProvider' => $dataProvider,
                'filterModel' => $model,
                'columns' => $representationCollection->getByName($uiModel->representation)->getColumns(),
                'rowOptions' => static function ($model): array {
                    return GridLegend::create(new SaleGridLegend($model))->gridRowOptions();
                },
            ]) ?>
    <?php $page->endBulkForm() ?>
    <?php $page->endContent() ?>
<?php $page->end() ?>
