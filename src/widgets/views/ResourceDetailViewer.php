<?php

use hipanel\modules\finance\grid\ResourceGridView;
use hipanel\modules\finance\helpers\ResourceConfigurator;
use hipanel\widgets\IndexPage;
use yii\data\DataProviderInterface;
use yii\db\ActiveRecordInterface;

/** @var DataProviderInterface $dataProvider */
/** @var ActiveRecordInterface $originalModel */
/** @var ActiveRecordInterface $originalSearchModel */
/** @var ResourceConfigurator $configurator */

?>

<div class="row">
    <div class="col-md-6 col-sm-12">
        <?php $page = IndexPage::begin(['model' => $originalSearchModel, 'layout' => 'resourceDetail']) ?>
            <?php $page->beginContent('title') ?>
                <?= Yii::t('hipanel', 'Resources') ?>
            <?php $page->endContent() ?>
            <?php $page->beginContent('table') ?>
                <?php $page->beginBulkForm() ?>
                    <?= ResourceGridView::widget([
                        'boxed' => false,
                        'configurator' => $configurator,
                        'dataProvider' => $dataProvider,
                        'filterModel' => $originalSearchModel,
                        'tableOptions' => [
                            'class' => 'table table-striped table-bordered',
                        ],
                        'columns' => [
                            'type',
                            'date',
                            'total',
                        ],
                    ]) ?>
                <?php $page->endBulkForm() ?>
            <?php $page->endContent() ?>
        <?php IndexPage::end() ?>
    </div>
</div>
