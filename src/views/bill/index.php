<?php

use hipanel\modules\finance\grid\BillGridView;
use hipanel\widgets\ActionBox;

$this->title                   = Yii::t('app', 'Bills');
$this->params['breadcrumbs'][] = $this->title;
$this->params['subtitle']      = Yii::$app->request->queryParams ? 'filtered list' : 'full list';

?>

<?php $box = ActionBox::begin(['model' => $model, 'bulk' => Yii::$app->user->can('own'), 'options' => ['class' => 'box-info']]) ?>
    <?php $box->beginActions() ?>
        <?php
            if (Yii::$app->user->can('support')) {
                print $box->renderCreateButton(Yii::t('app', 'Add payment')) . '&nbsp;';
            }
            if (Yii::$app->user->can('own')) {
                print $box->renderCreateButton(Yii::t('app', 'Edit')) . '&nbsp;';
                print $box->renderCreateButton(Yii::t('app', 'Delete')) . '&nbsp;';
            }
            print $box->renderCreateButton(Yii::t('app', 'Recharge account'));
        ?>
        <?= $box->renderSearchButton() ?>
    <?php $box->endActions() ?>

    <?php
    if (Yii::$app->user->can('own')) {
        print $box->renderBulkActions([
            'items' => [
                $box->renderBulkButton(Yii::t('app', 'Delete'), 'delete'),
            ],
        ]);
    }
    ?>
    <?= $box->renderSearchForm(compact('paymentType')) ?>
<?php $box::end() ?>

<?php $box->beginBulkForm() ?>
    <?= billGridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns'      => [
            'checkbox',
            'seller_id', 'client_id',
            'time', 'sum', 'balance', 'gtype', 'description',
        ],
    ]) ?>
<?php $box::endBulkForm() ?>