<?php

use hipanel\modules\finance\grid\SaleGridView;
use hipanel\widgets\IndexPage;
use yii\data\ArrayDataProvider;


?>

<?php $page = IndexPage::begin(['model' => $target, 'layout' => 'noSearch']) ?>

<?php $page->beginContent('show-actions') ?>
    <h4 class="box-title" style="display: inline-block;">&nbsp;<?= Yii::t('hipanel:finance', 'Sales') ?></h4>
<?php $page->endContent() ?>

<?php $page->beginContent('table') ?>
    <?php $page->beginBulkForm() ?>
        <?= SaleGridView::widget([
            'boxed' => false,
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $target->sales,
                'pagination' => [
                    'pageSize' => 25,
                ],
            ]),
            'tableOptions' => [
                'class' => 'table table-striped table-bordered',
            ],
            'columns' => [
                'time', 'unsale_time', 'tariff'
            ],
        ]) ?>
    <?php $page->endBulkForm() ?>
<?php $page->endContent() ?>

<?php $page::end() ?>
