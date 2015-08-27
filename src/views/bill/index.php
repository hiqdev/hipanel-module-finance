<?php

use hipanel\modules\finance\grid\BillGridView;
use hipanel\widgets\ActionBox;

$this->title                   = Yii::t('app', 'Bills');
$this->params['breadcrumbs'][] = $this->title;
$this->params['subtitle']      = Yii::$app->request->queryParams ? 'filtered list' : 'full list';

?>

<?php $box = ActionBox::begin(['model' => $model, 'dataProvider' => $dataProvider, 'bulk' => Yii::$app->user->can('manage')]) ?>
    <?php $box->beginActions() ?>
        <?php
            if (Yii::$app->user->can('manage')) {
                print $box->renderCreateButton(Yii::t('app', 'Add payment')) . '&nbsp;';
            }
            print $box->renderCreateButton(Yii::t('app', 'Recharge account'));
        ?>
        <?= $box->renderSearchButton() ?>
        <?= $box->renderSorter([
            'attributes' => [
                'seller',
                'client',
                'sum',
                'balance',
                'type',
                'descr'
            ],
        ]) ?>
        <?= $box->renderPerPage(); ?>
    <?php $box->endActions() ?>

    <?php if (Yii::$app->user->can('manage')) { ?>
        <?= $box->renderBulkActions([
            'items' => [
                $box->renderBulkButton(Yii::t('app', 'Edit'), 'edit'),
                $box->renderDeleteButton(),
            ],
        ]) ?>
    <?php } ?>
    <?= $box->renderSearchForm(compact('paymentType')) ?>
<?php $box->end() ?>

<?php $box->beginBulkForm() ?>
    <?= billGridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $model,
        'columns'      => [
            'checkbox',
            'seller_id', 'client_id',
            'time', 'sum', 'balance', 'gtype', 'description',
        ],
    ]) ?>
<?php $box->endBulkForm() ?>
