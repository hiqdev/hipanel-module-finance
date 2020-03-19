<?php

use yii\helpers\Html;

/**
 * @var \yii\web\View
 * @var \hipanel\modules\finance\cart\AbstractPurchase[] $success
 * @var \hipanel\modules\finance\cart\ErrorPurchaseException[] $error
 * @var \hipanel\modules\finance\cart\PendingPurchaseException[] $pending
 * @var array $remarks
 * @var float $balance
 * @var string $currency
 */

$this->title = Yii::t('cart', 'Order execution');

?>

<div class="cart-finish">

    <?php if (!empty($error)) : ?>
        <div class="row">
            <div class="col-md-12">
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
                                        <?= Html::tag('p', $exception->getMessage(), ['class' => 'bg-danger', 'style' => 'padding: 1em;']) ?>
                                    </td>
                                    <td align="right"
                                        class="text-bold"><?= Yii::$app->formatter->asCurrency($item->cost, $item->getCurrency()) ?></td>
                                    <td></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>


    <?php if (!empty($pending)) : ?>
        <div class="row">
            <div class="col-md-12">
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
                                    <td align="right"
                                        class="text-bold"><?= Yii::$app->formatter->asCurrency($item->cost, $item->getCurrency()) ?></td>
                                    <td></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (count($success)) : ?>
        <div class="row">
            <div class="col-md-12">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <?= Yii::t('cart', 'Operations performed') ?>
                            : <?= Yii::t('cart', '{0, plural, one{# position} other{# positions}}', count($success)) ?>
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
                                    <td align="right"
                                        class="text-bold"><?= Yii::$app->formatter->asCurrency($item->cost, $item->getCurrency()) ?></td>
                                    <td></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endif ?>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <?= Yii::t('hipanel:finance', 'Your balance after all operations: {amount}', [
                            'amount' => Yii::$app->formatter->asCurrency($balance, $currency),
                        ]) ?>
                    </h3>
                </div>
                <div class="box-body text-center">
                    <p class="text-muted well well-sm no-shadow">
                        <?php
                        if (Yii::$app->user->isGuest) {
                            echo Yii::t('hipanel:finance', 'If you have any further questions, please, contact us {emailLink}', [
                                'emailLink' => Html::mailto(Yii::$app->params['supportEmail'], Yii::$app->params['supportEmail']),
                            ]);
                        } else {
                            echo Yii::t('hipanel:finance', 'If you have any further questions, please, {ticketCreationLink}.', [
                                'ticketCreationLink' => Html::a(Yii::t('hipanel:finance', 'create a ticket'), '@ticket/create'),
                            ]);
                        } ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <?php if (count($remarks)) : ?>
        <?php foreach ($remarks as $remark) : ?>
            <?= $remark ?>
        <?php endforeach ?>
    <?php endif ?>

</div>
