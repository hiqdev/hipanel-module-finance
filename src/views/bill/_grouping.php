<?php

use hipanel\widgets\IndexPage;
use hipanel\modules\finance\models\Charge;
use hipanel\modules\finance\helpers\ChargesGrouper;
use hipanel\modules\finance\grid\GroupedByServerChargesGridView;
use \yii\data\ArrayDataProvider;

/**
 * @var \hipanel\modules\finance\models\Charge $model
 * @var IndexPage $page
 * @var \hipanel\modules\finance\helpers\ChargesGrouper $grouper
 * @var Charge[] $idToNameObject
 * @var Charge[][] $chargesByMainObject
 */

[$idToNameObject, $chargesByMainObject] = $grouper->group();

?>
<?php $page->beginContent('table') ?>
    <?php $page->beginBulkForm() ?>
        <?= GroupedByServerChargesGridView::widget([
            'boxed' => false,
            'showHeader' => false,
            'chargesByMainObject' => $chargesByMainObject,
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $idToNameObject,
                'pagination' => false,
            ]),
            'summaryRenderer' => function () {
                return ''; // remove unnecessary summary
            },
            'columns' => [
                'common_object_link',
            ],
        ]) ?>
    <?php $page->endBulkForm() ?>
<?php $page->endContent() ?>
