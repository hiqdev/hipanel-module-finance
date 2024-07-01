<?php

use hipanel\modules\finance\grid\TargetGridView;
use hipanel\widgets\IndexPage;
use yii\data\ArrayDataProvider;


?>

<?php $page = IndexPage::begin(['model' => $target, 'layout' => 'noSearch']) ?>

<?php $page->beginContent('show-actions') ?>
    <h4 class="box-title" style="display: inline-block;">&nbsp;<?= Yii::t('hipanel:finance', 'Sales') ?></h4>
<?php $page->endContent() ?>

<?php $page->beginContent('table') ?>
    <?php $page->beginBulkForm() ?>
        <?= TargetGridView::detailView([
            'boxed' => false,
            'model' => $target,
            'columns' => [
                'finished_sales',
                'active_sales',
                'future_sales',
            ],
        ]) ?>
    <?php $page->endBulkForm() ?>
<?php $page->endContent() ?>

<?php $page::end() ?>
