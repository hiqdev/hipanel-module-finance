<?php
use yii\web\View;

/**
 * @var View
 * @var \hipanel\modules\finance\widgets\PayButtonComment $widget
 */

?>

<br />

<strong class="text-warning"><?= Yii::t('hipanel:finance:deposit', 'Attention!') ?></strong>
<?= Yii::t('hipanel:finance:deposit', 'Your PayPal account must match the contact email address in this panel.') ?>
<br>
<?= Yii::t('hipanel:finance:deposit', 'The payments that do not meet this condition will be returned to the payer\'s PayPal account.') ?>
