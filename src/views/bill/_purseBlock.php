<?php

use hipanel\modules\finance\grid\PurseGridView;
use hipanel\modules\finance\models\Purse;
use hipanel\widgets\Box;
use hipanel\widgets\ModalButton;
use yii\helpers\Html;

?>

<?php $purse = new Purse($purse) ?>
<?php $box = Box::begin(['renderBody' => false]) ?>
    <?php $box->beginHeader() ?>
        <?= $box->renderTitle(Yii::t('hipanel/finance', '<b>{currency}</b> account', ['currency' => strtoupper($purse->currency)]), '&nbsp;') ?>
        <?php $box->beginTools() ?>
            <?php if (Yii::$app->user->can('manage')) : ?>
                <?= Html::a(Yii::t('hipanel/finance', 'See new invoice'), ['@purse/generate-invoice', 'id' => $purse->id], ['class' => 'btn btn-default btn-xs']) ?>
                <?= ModalButton::widget([
                    'model'    => $purse,
                    'form'     => ['action' => ['@purse/update-monthly-invoice']],
                    'button'   => ['label' => Yii::t('hipanel/finance', 'Update invoice'), 'class' => 'btn btn-default btn-xs'],
                    'body'     => Yii::t('hipanel/finance', 'Are you sure you want to update invoice?') . '<br>' .
                                  Yii::t('hipanel/finance', 'Current invoice will be substituted with newer version!'),
                    'modal'    => [
                        'header'        => Html::tag('h4', Yii::t('hipanel/finance', 'Confirm invoice updating')),
                        'headerOptions' => ['class' => 'label-warning'],
                        'footer'        => [
                            'label' => Yii::t('app', 'Update'),
                            'class' => 'btn btn-warning',
                            'data-loading-text' => Yii::t('app', 'Updating...'),
                        ],
                    ],
                ]) ?>
            <?php else : ?>
                <?= Html::a(Yii::t('app', 'Recharge account'), '#', ['class' => 'btn btn-default btn-xs']) ?>
            <?php endif ?>
        <?php $box->endTools() ?>
    <?php $box->endHeader() ?>
    <?php $box->beginBody() ?>
        <?= PurseGridView::detailView([
            'boxed' => false,
            'model' => $purse,
            'columns' => $purse->currency === 'usd'
                ? ['balance', 'credit', 'invoices']
                : ['balance', 'invoices'],
        ]) ?>
    <?php $box->endBody() ?>
<?php $box->end() ?>
