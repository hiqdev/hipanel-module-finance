<?php

use yii\helpers\Html;

?>
<section class="invoice">
    <div class="row">
        <div class="col-xs-12">
            <h2 class="page-header">
                <i class="fa fa-shopping-cart"></i> &nbsp;
                <?= Yii::t('cart', 'Operations performed') ?>: &nbsp; <?= count($done) ?> <?= Yii::t('cart', 'postitions') ?>
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
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($done as $item) : ?>
                        <tr>
                            <td class=text-center><?php static $no;echo ++$no; ?></td>
                            <td><?= $item->icon . ' ' . $item->name . ' ' . Html::tag('span', $item->description, ['class' => 'text-muted']) ?>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>

    <h2 class="page-header">
        <?= Yii::t('app', 'Your balance after all operations') ?>:
        <b><?= Yii::$app->formatter->format($balance, ['currency', 'currency' => 'usd']) ?></b>
    </h2>

    <p style="font-size:120%">
        {Lang:If you have any further questions}, {lang:please,}
        <?php if (Yii::$app->user->isGuest) : ?>
            <?= Yii::t('app', 'contact us') . Html::a(Yii::$app->params['supportEmail'], 'mailto:' . Yii::$app->params['supportEmail']) ?>.
        <?php else : ?>
            <?= Html::a(Yii::t('app', 'create a ticket'), '@ticket/create') ?>.
        <?php endif ?>
    </p>

</section>
