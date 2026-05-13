<?php

declare(strict_types=1);

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var View    $this
 * @var array[] $groups   group metadata: client, client_id, currency, count, total_sum
 * @var int[]   $validIds IDs of valid (Ongoing/Interrupted) installment plans
 */

if (empty($validIds)) : ?>
    <div class="alert alert-warning">
        <?= Yii::t('hipanel:finance', 'No installment plans in Ongoing or Interrupted state were selected.') ?>
    </div>
    <button type="button" class="btn btn-default" data-dismiss="modal">
        <?= Yii::t('hipanel', 'Close') ?>
    </button>
<?php return;
endif ?>

<form method="POST" action="<?= Url::toRoute(['create-bill']) ?>">
    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
    <?= Html::hiddenInput('confirmed', '1') ?>
    <?php foreach ($validIds as $id) : ?>
        <?= Html::hiddenInput('selection[]', $id) ?>
    <?php endforeach ?>

    <p><?= Yii::t('hipanel:finance', 'The following bills will be created:') ?></p>

    <table class="table table-bordered table-condensed">
        <thead>
            <tr>
                <th><?= Yii::t('hipanel', 'Client') ?></th>
                <th><?= Yii::t('hipanel:finance', 'Currency') ?></th>
                <th><?= Yii::t('hipanel:finance', 'Plans') ?></th>
                <th><?= Yii::t('hipanel:finance', 'Total sum') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($groups as $group) : ?>
                <tr>
                    <td><?= Html::encode($group['client']) ?></td>
                    <td><?= Html::encode(mb_strtoupper($group['currency'])) ?></td>
                    <td><?= $group['count'] ?></td>
                    <td><?= Yii::$app->formatter->asCurrency($group['total_sum'], $group['currency']) ?></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>

    <div class="row">
        <div class="col-xs-12">
            <?= Html::submitButton(
                Yii::t('hipanel:finance', 'Proceed to bill form'),
                ['class' => 'btn btn-success'],
            ) ?>
            <?= Html::button(
                Yii::t('hipanel', 'Cancel'),
                ['class' => 'btn btn-default', 'data-dismiss' => 'modal'],
            ) ?>
        </div>
    </div>
</form>
