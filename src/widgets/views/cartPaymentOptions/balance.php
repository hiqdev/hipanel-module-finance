<?php

use yii\helpers\Html;

/**
 * @var \yii\web\View $this
 * @var \hiqdev\yii2\cart\ShoppingCart $cart
 * @var \hipanel\modules\client\models\Client $client
 */

$formatter = Yii::$app->formatter;

?>

<h4><?= Yii::t('hipanel:finance', 'Your balance') ?></h4>

<?php foreach ($client->purses as $purse): ?>
    <h4>
        <?= Yii::t('hipanel:finance', '{balance} {formattedCredit}', [
            'balance' => $formatter->asCurrency($purse->balance, $purse->currency),
            'formattedCredit' => $purse->credit > 0
                ? Yii::t('hipanel:finance', '(+{amount} of credit)', [
                    'amount' => $formatter->asCurrency($purse->credit, $purse->currency),
                ])
                : '',
        ]) ?>
    </h4>
<?php endforeach ?>
