<?php

use hipanel\modules\finance\grid\PurseGridView;
use hipanel\widgets\Box;
use yii\helpers\Html;

$user = Yii::$app->user;
$client = $model->clientModel;
$isEmployee = $client->type === $client::TYPE_EMPLOYEE;
$documentType = $isEmployee ? 'acceptance' : 'invoice';

?>

<?php $box = Box::begin(['renderBody' => false]) ?>
    <?php $box->beginHeader() ?>
        <?= $box->renderTitle(Yii::t('hipanel:finance', '<b>{currency}</b> account', ['currency' => strtoupper($model->currency)]), '&nbsp;') ?>
        <?php $box->beginTools() ?>
            <?php if ($user->can('deposit')) : ?>
                <?= Html::a(Yii::t('hipanel', 'Recharge account'), '#', ['class' => 'btn btn-default btn-xs']) ?>
            <?php endif ?>
        <?php $box->endTools() ?>
    <?php $box->endHeader() ?>
    <?php $box->beginBody() ?>
        <?= PurseGridView::detailView([
            'boxed' => false,
            'model' => $model,
            'columns' => array_filter([
                $user->can('bill.read') ? 'balance' : null,
                $user->can('bill.read') && $model->currency === 'usd' ? 'credit' : null,
                'contact',
                'requisite',
                $user->can('document.read') && $user->can('bill.read') ? ($isEmployee ? 'acceptances' : 'invoices') : null,
                $user->can('document.read') && $isEmployee ? 'contracts' : null,
                $user->can('document.read') && $isEmployee ? 'probations' : null,
                $user->can('document.read') && $isEmployee ? 'ndas' : null,
            ]),
        ]) ?>
    <?php $box->endBody() ?>
<?php $box->end() ?>
