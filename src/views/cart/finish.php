<?php

use yii\helpers\Html;

?>
<section class="invoice">
    <div class="row">
        <div class="col-xs-12">
            <h2 class="page-header">
                <i class="fa fa-shopping-cart"></i> &nbsp;
                <?= Yii::t('cart', 'Operations performed') ?>: &nbsp; <?= Yii::t('cart', '{0, plural, one{# position} other{# positions}}', count($done)) ?>
            </h2>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 table-responsive">
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
                    <?php foreach ($done as $item) : ?>
                        <tr>
                            <td class="text-center text-bold"><?php static $no;echo ++$no; ?></td>
                            <td><?= $item->icon . ' ' . $item->name . ' ' . Html::tag('span', $item->description, ['class' => 'text-muted']) ?>
                            <td align=right class=text-bold><?= Yii::$app->formatter->format($item->cost, ['currency', 'currency' => 'usd']) ?></td>
                            <td></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<section class="invoice">
    <h2 class="page-header">
        <?= Yii::t('app', 'Your balance after all operations') ?>:
        <b><?= Yii::$app->formatter->format($balance, ['currency', 'currency' => 'usd']) ?></b>
    </h2>

    <p style="font-size:120%">
        <?= Yii::t('app', 'If you have any further questions') ?>, <?= Yii::t('app', 'please') ?>,
        <?php if (Yii::$app->user->isGuest) : ?>
            <?= Yii::t('app', 'contact us') . Html::a(Yii::$app->params['supportEmail'], 'mailto:' . Yii::$app->params['supportEmail']) ?>.
        <?php else : ?>
            <?= Html::a(Yii::t('app', 'create a ticket'), '@ticket/create') ?>.
        <?php endif ?>
    </p>
</section>
