<?php

/**
 * @var \yii\data\ActiveDataProvider $dataProvider
 * @var \yii\web\View $this
 * @var \hipanel\modules\finance\models\ChargeSearch $model
 * @var array $billTypes
 * @var array $billGroupLabels
 */

use hipanel\modules\finance\grid\ChargeGridView;
use hipanel\widgets\IndexPage;
use hipanel\widgets\Pjax;

$this->title = Yii::t('hipanel:finance', 'Charges');
$this->params['subtitle'] = array_filter(Yii::$app->request->get($model->formName(), [])) ? Yii::t('hipanel', 'filtered list') : Yii::t('hipanel', 'full list');
$this->params['breadcrumbs'][] = $this->title;

?>

<?php Pjax::begin(array_merge(Yii::$app->params['pjax'], ['enablePushState' => true])) ?>
    <?php $page = IndexPage::begin(compact('model', 'dataProvider')) ?>

        <?= $page->setSearchFormData(compact(['billTypes', 'billGroupLabels'])) ?>

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
<?php Pjax::end() ?>

