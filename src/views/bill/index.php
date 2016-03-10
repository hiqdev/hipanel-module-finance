<?php

use hipanel\modules\finance\grid\BillGridView;
use hipanel\widgets\ActionBox;
use yii\helpers\Html;

$this->title                   = Yii::t('app', 'Bills');
$this->params['breadcrumbs'][] = $this->title;
$this->params['subtitle']      = array_filter(Yii::$app->request->get($model->formName(), [])) ? 'filtered list' : 'full list';

?>

<?php $box = ActionBox::begin(['model' => $model, 'dataProvider' => $dataProvider, 'bulk' => true]) ?>
    <?php $box->beginActions() ?>
        <?php
            if (Yii::$app->user->can('manage')) {
                echo $box->renderCreateButton(Yii::t('app', 'Add payment')) . '&nbsp;';
            }
            echo Html::a(Yii::t('hipanel/finance', 'Recharge account'), ['@pay/deposit'], ['class' => 'btn btn-success']);
        ?>
        <?= $box->renderSearchButton() ?>
        <?= $box->renderSorter([
            'attributes' => [
                'seller',
                'client',
                'sum',
                'balance',
                'type',
                'descr',
            ],
        ]) ?>
        <?= $box->renderPerPage(); ?>
    <?php $box->endActions() ?>
    <?= $box->renderBulkActions([
        'items' => [
//            TODO: implement bills edit
//            Yii::$app->user->can('manage')       ? $box->renderBulkButton(Yii::t('app', 'Edit'), 'edit') : null,
            Yii::$app->user->can('delete-bills') ? $box->renderDeleteButton() : null,
        ],
    ]) ?>
    <?= $box->renderSearchForm(compact('type')) ?>
<?php $box->end() ?>

<?php $box->beginBulkForm() ?>
    <?= BillGridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $model,
        'columns'      => [
            'checkbox', 'client_id', 'time', 'sum', 'balance',
            'type_label', 'description',
        ],
    ]) ?>
<?php $box->endBulkForm() ?>
