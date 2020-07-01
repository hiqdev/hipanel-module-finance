<?php
use yii\web\View;

/**
 * @var View
 * @var \hipanel\modules\finance\widgets\PayButtonComment $widget
 */
?>

<br />

<strong class="text-warning"><?= Yii::t('hipanel:finance:deposit', 'Attention!') ?></strong>
<?= Yii::t('hipanel:finance:deposit', 'Please be informed that due to the difficulties in cash withdrawal from Paxum to the bank account,<br/>we open the possibility to pay using this payment method, but we  have the right to refund you the whole amount<br/> and delete the credit from your account in control panel in case of fail of our possibility to withdraw the funds.') ?>
