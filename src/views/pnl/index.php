<?php

use hipanel\models\IndexPageUiOptions;
use hipanel\modules\finance\grid\BillRepresentations;
use hipanel\modules\finance\grid\PnlGridView;
use hipanel\modules\finance\models\PnlSearch;
use hipanel\widgets\IndexPage;
use hiqdev\hiart\ActiveDataProvider;
use yii\web\View;

/**
 * @var View $this
 * @var PnlSearch $model
 * @var IndexPageUiOptions $uiModel
 * @var BillRepresentations $representationCollection
 * @var ActiveDataProvider $dataProvider
 */

$this->title = Yii::t('hipanel:finance', 'All P&L records');
$this->params['breadcrumbs'][] = $this->title;

?>

<?php $page = IndexPage::begin(['model' => $model, 'dataProvider' => $dataProvider]) ?>

<?php $page->beginContent('table') ?>
    <?php $page->beginBulkForm() ?>
        <?= PnlGridView::widget([
            'boxed' => false,
            'dataProvider' => $dataProvider,
            'filterModel' => $model,
            'columns' => [
                'charge_id',
                'type',
                'month',
                'currency',
                'sum',
                'charge_sum',
                'discount_sum',
            ],
        ]) ?>
    <?php $page->endBulkForm() ?>
<?php $page->endContent() ?>

<?php IndexPage::end() ?>
