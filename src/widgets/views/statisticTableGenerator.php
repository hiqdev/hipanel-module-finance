<?php

/** @var array $statistic */

?>

<div class="table-responsive">
    <table class="table no-margin">
        <thead>
        <tr>
            <th><?= Yii::t('hipanel:document', 'Date') ?></th>
            <th><?= Yii::t('hipanel:document', 'Count') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($statistic as $date => $count) : ?>
            <tr>
                <td><?= Yii::$app->formatter->asDate($date, 'php:Y-m-d') ?></td>
                <td><?= $count ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
