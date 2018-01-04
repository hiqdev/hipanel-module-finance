<?php

use hipanel\modules\finance\grid\PriceGridView;
use hipanel\widgets\IndexPage;
use hipanel\widgets\Pjax;
use yii\helpers\Html;

$this->title = Yii::t('hipanel:finance', 'Price');
$this->params['breadcrumbs'][] = $this->title;

?>

<?php Pjax::begin(array_merge(Yii::$app->params['pjax'], ['enablePushState' => true])) ?>
    <?php $page = IndexPage::begin(compact('model', 'dataProvider')) ?>

        <?php $page->setSearchFormData([]) ?>

        <?php $page->beginContent('main-actions') ?>
            <?= Html::a(Yii::t('hipanel', 'Create'), ['/finance/price/create'], ['class' => 'btn btn-sm btn-success']) ?>
        <?php $page->endContent() ?>

        <?php $page->beginContent('sorter-actions') ?>
            <?= $page->renderSorter(['attributes' => ['id']]) ?>
        <?php $page->endContent() ?>

        <?php $page->beginContent('table') ?>
            <?php $page->beginBulkForm() ?>
                <?= PriceGridView::widget([
                    'boxed' => false,
                    'dataProvider' => $dataProvider,
                    'filterModel' => $model,
                    'columns' => [
                        'checkbox',
                        'price',
                        'currency',
                        'unit',
                        'plan',
                        'type',
                    ],
                ]) ?>
            <?php $page->endBulkForm() ?>
        <?php $page->endContent() ?>
    <?php $page->end() ?>
<?php Pjax::end() ?>
