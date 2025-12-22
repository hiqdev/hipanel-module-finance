<?php

use hipanel\modules\finance\grid\HistorySalesGridView;
use hipanel\modules\finance\models\Target;
use hipanel\widgets\IndexPage;

/** @var Target $target */

?>

<?php $page = IndexPage::begin(['model' => $target, 'layout' => 'noSearch']) ?>

<?php $page->beginContent('show-actions') ?>
    <h4 class="box-title" style="display: inline-block;">&nbsp;<?= Yii::t('hipanel:finance', 'Sales') ?></h4>
<?php $page->endContent() ?>

<?php $page->beginContent('table') ?>
    <?php $page->beginBulkForm() ?>
        <?= HistorySalesGridView::detailView([
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
