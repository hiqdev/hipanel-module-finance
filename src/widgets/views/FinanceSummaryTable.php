<?php

use hipanel\modules\finance\widgets\ColoredBalance;
use yii\base\DynamicModel;

/**
 * @var string $tableName
 * @var array $currencies
 * @var array $rows
 * @var array $values
 */

?>

<div class="summary">
    <div style="display: flex; flex-wrap: wrap; margin: -1rem">
        <div class="table-responsive" style="margin: 1rem">
            <table class="table table-striped table-bordered table-condensed">
                <thead>
                <tr>
                    <td class="text-muted"><?= $tableName ?></td>
                    <?php foreach ($currencies as $sign): ?>
                        <th class="text-right"><?= $sign ?></th>
                    <?php endforeach ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $type => $label): ?>
                    <tr>
                        <td class="text-right"><?= $label ?></td>
                        <?php if ($type === 'eurAmount') : ?>
                            <td class="text-center text-bold" colspan="<?= count($currencies) ?>">
                                <?= Yii::$app->formatter->asCurrency($values[$type]['eur'], 'eur') ?>
                            </td>
                        <?php else : ?>
                            <?php foreach (array_keys($currencies) as $currency): ?>
                                <td class="text-right">
                                    <?= isset($values[$type][$currency]) ? ColoredBalance::widget([
                                        'model' => new DynamicModel(['sum' => $values[$type][$currency], 'currency' => $currency]),
                                        'attribute' => 'sum',
                                    ]) : '' ?>
                                </td>
                            <?php endforeach ?>
                        <?php endif ?>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
