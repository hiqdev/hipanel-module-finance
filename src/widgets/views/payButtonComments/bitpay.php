<?php

use yii\helpers\Html;
use yii\web\View;

/**
 * @var View
 * @var \hipanel\modules\finance\widgets\PayButtonComment $widget
 */
?>

<br />

<?php if ($widget->getPayButton()->isDisabled()) : ?>
    <?= Yii::t('hipanel:finance:deposit', 'Payments with BitPay are available only after verification of the {accountLink} and {phoneNumberLink}.', [
        'accountLink' => Html::a(Yii::t('hipanel:finance:deposit', 'account'), ['@contact/attach-documents', 'id' => Yii::$app->user->getId()]),
        'phoneNumberLink' => Html::a(Yii::t('hipanel:finance:deposit', 'phone number'), ['@client/view', 'id' => Yii::$app->user->getId()]),
    ]) ?>
    <br>
    <?= Yii::t('hipanel:finance:deposit', 'In case of any questions, please {contactSupportTeamLink}.', [
        'contactSupportTeamLink' => Html::a(Yii::t('hipanel:finance:deposit', 'contact support team'), ['@ticket/create']),
    ]) ?>
<?php else: ?>
    <strong class="text-warning"><?= Yii::t('hipanel:finance:deposit', 'Attention!') ?></strong>
    <?= Yii::t('hipanel:finance:deposit', 'Daily limit on the payment is 10.000 USD.') ?>
    <br>
    <?= Yii::t('hipanel:finance:deposit', 'If you receive \'limit exceeded\' error, please try again next day.') ?>
    <br>
     <?= Yii::t('hipanel:finance:deposit', 'We apologize for inconveniences and hope for your understanding.') ?>
<?php endif; ?>
