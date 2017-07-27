<?php
use yii\web\View;

/**
 * @var View
 * @var \hipanel\modules\finance\widgets\PayButtonComment $widget
 */
?>

<br />

<strong class="text-warning"><?= Yii::t('hipanel:finance:deposit', 'Attention!') ?></strong>
<?= Yii::t('hipanel:finance:deposit', 'Daily limit on the payment is 10.000 USD.') ?>
<br>
<?= Yii::t('hipanel:finance:deposit', 'If you receive \'limit exceeded\' error, please try again next day.') ?>
<br>
 <?= Yii::t('hipanel:finance:deposit', 'We apologize for inconveniences and hope for your understanding.') ?>
