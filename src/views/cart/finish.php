<?php

use hipanel\base\View;
use yii\helpers\Html;

/**
 * @var \hipanel\modules\finance\cart\AbstractCartPosition[] $success
 * @var \hipanel\modules\finance\cart\ErrorPurchaseException[] $error
 * @var View $this
 */

$this->title = Yii::t('hipanel/finance', 'Cart finishing');

if (!empty($error)) { ?>
    <section class="invoice">
        <div class="row">
            <div class="col-xs-12">
                <h2 class="page-header bg-danger"> <?php // TODO: design block for errors ?>
                    <i class="fa fa-shopping-cart"></i> &nbsp;
                    <?= Yii::t('cart', 'Operations failed') ?>:&nbsp;<?= Yii::t('cart', '{0, plural, one{# position} other{# positions}}', count($error)) ?>
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
                    <?php
                    $no = 1;
                    foreach ($error as $exception) :
                        $item = $exception->position;
                        ?>
                        <tr>
                            <td class="text-center text-bold"><?= $no++ ?></td>
                            <td>
                                <?= $item->icon . ' ' . $item->name . ' ' . Html::tag('span', $item->description, ['class' => 'text-muted']) ?><br>
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
    </section>
<?php } ?>

<?php if (count($success)) { ?>
<section class="invoice">
    <div class="row">
        <div class="col-xs-12">
            <h2 class="page-header">
                <i class="fa fa-shopping-cart"></i> &nbsp;
                <?= Yii::t('cart', 'Operations performed') ?>: <?= Yii::t('cart', '{0, plural, one{# position} other{# positions}}', count($success)) ?>
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
                <?php $no = 1;
                foreach ($success as $item) : ?>
                    <tr>
                        <td class="text-center text-bold"><?= $no++ ?></td>
                        <td><?= $item->icon . ' ' . $item->name . ' ' . Html::tag('span', $item->description, ['class' => 'text-muted']) ?>
                        <td align="right" class="text-bold"><?= Yii::$app->formatter->format($item->cost, ['currency', 'currency' => 'usd']) ?></td>
                        <td></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<?php } ?>

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
