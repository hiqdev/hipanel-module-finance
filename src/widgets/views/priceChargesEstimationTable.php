<?php

/**
 * @var \yii\web\View $this
 * @var array $charges
 */

use yii\helpers\Html;

$i = 1;

?>

<table class="table table-condensed">
    <tr>
        <th><?= Yii::t('hipanel', '#') ?></th>
        <th><?= Yii::t('hipanel', 'Type') ?></th>
        <th><?= Yii::t('hipanel', 'Sum') ?></th>
    </tr>
<?php foreach ($charges as $charge): ?>
    <tr>
        <td><?= $i++ ?>.</td>
        <td>
            <?php [$type] = explode(',', $charge['type']); ?>
            <?php
            echo Yii::t('hipanel.finance.priceTypes', \yii\helpers\Inflector::titleize($type));
            if ($charge['comment']) {
               echo ': ' . Html::tag('i', $charge['comment']);
            }
            ?>
        </td>
        <td><?= $charge['formattedPrice'] ?></td>
    </tr>
<?php endforeach ?>
</table>
