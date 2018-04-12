<?php

use hipanel\modules\finance\grid\PriceGridView;
use hipanel\widgets\IndexPage;
use hipanel\widgets\Pjax;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('hipanel:finance', 'Price');
$this->params['breadcrumbs'][] = $this->title;

/**
 * @var \hiqdev\hiart\ActiveDataProvider $dataProvider
 * @var \hipanel\modules\finance\models\Price $model
 */

?>

<?php Pjax::begin(array_merge(Yii::$app->params['pjax'], ['enablePushState' => true])) ?>
    <?php $page = IndexPage::begin(compact('model', 'dataProvider')) ?>

        <?php $page->setSearchFormData([]) ?>

        <?php $page->beginContent('sorter-actions') ?>
            <?= $page->renderSorter(['attributes' => ['id']]) ?>
        <?php $page->endContent() ?>

        <?php $page->beginContent('bulk-actions') ?>
            <?= $page->renderBulkButton(Yii::t('hipanel', 'Update'), Url::to(['@price/update']), 'warning') ?>
            <?= $page->renderBulkButton(Yii::t('hipanel', 'Delete'), Url::to(['@price/delete']), 'danger') ?>
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
                        'plan'
                    ],
                ]) ?>
            <?php $page->endBulkForm() ?>
        <?php $page->endContent() ?>
    <?php $page->end() ?>
<?php Pjax::end() ?>
