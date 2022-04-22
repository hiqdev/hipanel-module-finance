<?php
declare(strict_types=1);

use hipanel\modules\finance\grid\GroupedByServerChargesGridView;
use hipanel\modules\finance\helpers\ChargesGrouper;
use hipanel\modules\finance\models\Charge;
use hipanel\widgets\IndexPage;
use yii\data\ArrayDataProvider;

/**
 * @var Charge $model
 * @var IndexPage $page
 * @var ChargesGrouper $grouper
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
            'summaryRenderer' => static fn(): string => '',
            'columns' => [
                'common_object_link',
            ],
        ]) ?>
    <?php $page->endBulkForm() ?>
<?php $page->endContent() ?>
