<?php

use hipanel\modules\finance\models\Bill;
use hipanel\modules\finance\widgets\ColoredBalance;

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
                        <?php foreach ($currencies as $currency => $sign): ?>
                            <td class="text-right">
                                <?= ColoredBalance::widget([
                                    'model' => new Bill([
                                        'sum' => $values[$type][$currency] ?? '',
                                        'currency' => $currency,
                                    ]),
                                    'attribute' => 'sum',
                                ]) ?>
                            </td>
                        <?php endforeach ?>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
