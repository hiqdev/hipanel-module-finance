<?php

use hipanel\modules\finance\grid\PriceGridView;
use hipanel\modules\finance\models\Price;
use hipanel\widgets\IndexPage;
use hiqdev\hiart\ActiveDataProvider;

/**
 * @var ActiveDataProvider $dataProvider
 * @var Price $model
 */

$this->title = Yii::t('hipanel:finance', 'Price');
$this->params['breadcrumbs'][] = $this->title;

?>

<?php $page = IndexPage::begin(['model' => $model, 'dataProvider' => $dataProvider]) ?>

    <?php $page->setSearchFormData([]) ?>

    <?php $page->beginContent('sorter-actions') ?>
        <?= $page->renderSorter(['attributes' => ['id']]) ?>
    <?php $page->endContent() ?>

    <?php $page->beginContent('bulk-actions') ?>
        <?php if (Yii::$app->user->can('price.create')) : ?>
            <?= $page->renderBulkButton('@price/update', Yii::t('hipanel', 'Update'), ['color' => 'warning']) ?>
        <?php endif ?>
        <?php if (Yii::$app->user->can('price.delete')) : ?>
            <?= $page->renderBulkDeleteButton('@price/delete') ?>
        <?php endif ?>
    <?php $page->endContent() ?>

    <?php $page->beginContent('table') ?>
        <?php $page->beginBulkForm() ?>
            <?= PriceGridView::widget([
                'boxed' => false,
                'dataProvider' => $dataProvider,
                'filterModel' => $model,
                'columns' => [
                    'checkbox',
                    'object->name',
                    'object->label',
                    'price',
                    'type',
                    'note',
                    'plan',
                ],
            ]) ?>
        <?php $page->endBulkForm() ?>
    <?php $page->endContent() ?>
<?php $page->end() ?>
