<?php

use hipanel\modules\finance\grid\PurseGridView;
use hipanel\modules\finance\models\Purse;
use hipanel\widgets\Box;
use hipanel\widgets\ModalButton;
use yii\helpers\Html;

$client = $model->clientModel;
$isEmployee = $client->type === $client::TYPE_EMPLOYEE;
$documentType = $isEmployee ? 'acceptance' : 'invoice';

?>

<?php $box = Box::begin(['renderBody' => false]) ?>
    <?php $box->beginHeader() ?>
        <?= $box->renderTitle(Yii::t('hipanel:finance', '<b>{currency}</b> account', ['currency' => strtoupper($model->currency)]), '&nbsp;') ?>
        <?php $box->beginTools() ?>
            <?php if (Yii::$app->user->can('manage')) : ?>
                <?= Html::a(
                    $isEmployee ? Yii::t('hipanel:finance', 'See new acceptance report') : Yii::t('hipanel:finance', 'See new invoice'),
                    ['@purse/generate-document', 'id' => $model->id, 'type' => $documentType],
                    ['class' => 'btn btn-default btn-xs', 'target' => 'new-invoice']
                ) ?>
                <?= ModalButton::widget([
                    'model'    => $model,
                    'form'     => [
                        'action' => ['@purse/generate-and-save-document'],
                    ],
                    'button'   => [
                        'label' => $isEmployee ? Yii::t('hipanel:finance', 'Update acceptance report') : Yii::t('hipanel:finance', 'Update invoice'),
                        'class' => 'btn btn-default btn-xs',
                    ],
                    'body'     => implode('', [
                        Html::activeHiddenInput($model, 'type', ['value' => $documentType]),
                        Yii::t('hipanel:finance', 'Are you sure you want to update document?') . '<br>',
                        Yii::t('hipanel:finance', 'Current document will be substituted with newer version!'),
                    ]),
                    'modal'    => [
                        'header'        => Html::tag('h4', Yii::t('hipanel:finance', 'Confirm document updating')),
                        'headerOptions' => ['class' => 'label-warning'],
                        'footer'        => [
                            'label' => Yii::t('hipanel', 'Update'),
                            'class' => 'btn btn-warning',
                            'data-loading-text' => Yii::t('hipanel', 'Updating...'),
                        ],
                    ],
                ]) ?>
            <?php elseif (Yii::$app->user->can('deposit')) : ?>
                <?= Html::a(Yii::t('hipanel', 'Recharge account'), '#', ['class' => 'btn btn-default btn-xs']) ?>
            <?php endif ?>
        <?php $box->endTools() ?>
    <?php $box->endHeader() ?>
    <?php $box->beginBody() ?>
        <?= PurseGridView::detailView([
            'boxed' => false,
            'model' => $model,
            'columns' => array_filter([
                'balance',
                $model->currency === 'usd' ? 'credit' : null,
                'contact', 'requisite',
                'invoices',
            ]),
        ]) ?>
    <?php $box->endBody() ?>
<?php $box->end() ?>
