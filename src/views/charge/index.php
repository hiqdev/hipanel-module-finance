<?php

use hipanel\modules\finance\grid\ChargeGridView;
use hipanel\modules\finance\models\ChargeSearch;
use hipanel\widgets\IndexPage;
use yii\data\ActiveDataProvider;
use yii\web\View;

/**
 * @var ActiveDataProvider $dataProvider
 * @var View $this
 * @var ChargeSearch $model
 * @var array $billTypes
 * @var array $billGroupLabels
 */

$this->title = Yii::t('hipanel:finance', 'Charges');
$this->params['subtitle'] = array_filter(Yii::$app->request->get($model->formName(), [])) ? Yii::t('hipanel', 'filtered list') : Yii::t('hipanel', 'full list');
$this->params['breadcrumbs'][] = $this->title;

?>

<?php $page = IndexPage::begin(['model' => $model, 'dataProvider' => $dataProvider]) ?>

    <?php $page->setSearchFormData(['billTypes' => $billTypes, 'billGroupLabels' => $billGroupLabels]) ?>

    <?php $page->beginContent('sorter-actions') ?>
        <?= $page->renderSorter([
            'attributes' => [
                'time',
            ],
        ]) ?>
    <?php $page->endContent() ?>

    <?php $page->beginContent('table') ?>
        <?php $page->beginBulkForm() ?>
            <?= ChargeGridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $model,
                'boxed' => false,
                'columns' => [
                    'client',
                    'seller',
                    'tariff',
                    'type_label',
                    'sum',
                    'name',
                    'quantity',
                    'time',
                    'is_payed',
                    'label',
                ],
            ]) ?>
        <?php $page->endBulkForm() ?>
    <?php $page->endContent() ?>

<?php $page->end() ?>

