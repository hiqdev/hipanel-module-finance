<?php

use hipanel\base\View;
use yii\helpers\Html;

/**
 * @var \hipanel\modules\finance\cart\AbstractCartPosition[] $success
 * @var \hipanel\modules\finance\cart\ErrorPurchaseException[] $error
 * @var \hipanel\modules\finance\cart\PendingPurchaseException[] $pending
 * @var array $remarks
 * @var View $this
 * @var float $balance
 */

$this->title = Yii::t('cart', 'Order execution');
?>

<?php if (!empty($error)) : ?>
    <div class="box box-danger box-solid">
        <div class="box-header with-border">
            <?= Html::tag('h3', '<i class="fa fa-exclamation-triangle"></i>&nbsp;&nbsp;' . Yii::t('cart', 'Operations failed') . ': ' . Yii::t('cart', '{0, plural, one{# position} other{# positions}}', count($error))); ?>
        </div>
        <div class="box-body">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th><?= Yii::t('cart', 'Description') ?></th>
                    <th class="text-right"><?= Yii::t('cart', 'Price') ?></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $no = 1;
                foreach ($error as $exception) :
                    $item = $exception->position;
                    ?>
                    <tr>
                        <td class="text-center text-bold"><?= $no++ ?></td>
                        <td>
                            <?= $item->renderDescription() ?><br>
                            <?= $exception->getMessage() ?>
                        </td>
                        <td align="right" class="text-bold"><?= Yii::$app->formatter->format($item->cost, ['currency', 'currency' => 'usd']) ?></td>
                        <td></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>


<?php if (!empty($pending)) : ?>
    <div class="box box-warning box-solid">
        <div class="box-header with-border">
            <?= Html::tag('h3', '<i class="fa fa-exclamation-triangle"></i>&nbsp;&nbsp;' . Yii::t('cart', 'Pending operations') . ': ' . Yii::t('cart', '{0, plural, one{# position} other{# positions}}', count($pending))); ?>
        </div>
        <div class="box-body">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th><?= Yii::t('cart', 'Description') ?></th>
                    <th class="text-right"><?= Yii::t('cart', 'Price') ?></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $no = 1;
                foreach ($pending as $exception) :
                    $item = $exception->position;
                    ?>
                    <tr>
                        <td class="text-center text-bold"><?= $no++ ?></td>
                        <td>
                            <?= $item->renderDescription() ?><br>
                            <?= $exception->getMessage() ?>
                        </td>
                        <td align="right" class="text-bold"><?= Yii::$app->formatter->format($item->cost, ['currency', 'currency' => 'usd']) ?></td>
                        <td></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php if (count($success)) : ?>
<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title">
            <?= Yii::t('cart', 'Operations performed') ?>: <?= Yii::t('cart', '{0, plural, one{# position} other{# positions}}', count($success)) ?>
        </h3>
    </div>
    <div class="box-body">
        <table class="table table-striped">
            <thead>
            <tr>
                <th class="text-center">#</th>
                <th><?= Yii::t('cart', 'Description') ?></th>
                <th><?= Yii::t('cart', 'Notes') ?></th>
                <th class="text-right"><?= Yii::t('cart', 'Price') ?></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1 ?>
            <?php foreach ($success as $purchase) : ?>
                <?php $item = $purchase->position ?>
                <tr>
                    <td class="text-center text-bold"><?= $no++ ?></td>
                    <td><?= $item->renderDescription() ?></td>
                    <td><?= $purchase->renderNotes() ?></td>
                    <td align="right" class="text-bold"><?= Yii::$app->formatter->format($item->cost, ['currency', 'currency' => 'usd']) ?></td>
                    <td></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</div>
<?php endif ?>

<?php if (count($remarks)) : ?>
    <?php foreach ($remarks as $remark) : ?>
        <?= $remark ?>
    <?php endforeach ?>
<?php endif ?>

<div class="row">
    <div class="col-md-5">
        <div class="box box-solid">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <?= Yii::t('app', 'Your balance after all operations') ?>: &nbsp;
                    <b><?= Yii::$app->formatter->format($balance, ['currency', 'currency' => 'usd']) ?></b>
                </h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body text-center">
                <p class="text-muted well well-sm no-shadow">
                    <?= Yii::t('app', 'If you have any further questions') ?>, <?= Yii::t('app', 'please') ?>,
                    <?php if (Yii::$app->user->isGuest) : ?>
                        <?= Yii::t('app', 'contact us') . ' ' . Html::a(Yii::$app->params['supportEmail'], 'mailto:' . Yii::$app->params['supportEmail']) ?>.
                    <?php else : ?>
                        <?= Html::a(Yii::t('app', 'create a ticket'), '@ticket/create') ?>.
                    <?php endif ?>
                </p>
            </div>
            <!-- /.box-body -->
        </div>
    </div>
</div>
