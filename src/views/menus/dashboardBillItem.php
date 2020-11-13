<?php

use hipanel\helpers\Url;
use hipanel\modules\client\models\Client;
use hipanel\modules\client\widgets\combo\ClientCombo;
use hipanel\modules\dashboard\widgets\SearchForm;
use hipanel\modules\dashboard\widgets\SmallBox;
use hipanel\modules\finance\models\BillSearch;
use yii\helpers\Html;

/** @var Client $model */

?>

<div class="col-lg-3 col-md-6 col-sm-12 col-xs-12">
    <?php $box = SmallBox::begin([
        'boxTitle' => Yii::t('hipanel:finance', 'Finance'),
        'boxIcon' => 'fa-money',
        'boxColor' => SmallBox::COLOR_RED,
    ]) ?>
    <?php $box->beginBody() ?>
    <span style="font-size: 18px"><?= Yii::$app->formatter->asCurrency($model->balance, $model->currency) ?></span>
    <br>
    <?php if ($model->credit > 0) : ?>
        <span><?= Yii::t('hipanel', 'Credit') . ' ' . Yii::$app->formatter->asCurrency($model->credit, $model->currency) ?></span>
    <?php endif ?>
    <?php if (Yii::$app->user->can('manage')) : ?>
        <br>
        <?= SearchForm::widget([
            'formOptions' => [
                'id' => 'bill-search',
                'action' => Url::to('@bill/index'),
            ],
            'model' => new BillSearch(),
            'attribute' => 'client_id',
            'inputWidget' => ClientCombo::class,
            'buttonColor' => SmallBox::COLOR_RED,
        ]) ?>
    <?php endif; ?>
    <?php $box->endBody() ?>
    <?php $box->beginFooter() ?>
    <?= Html::a(Yii::t('hipanel', 'View') . $box->icon(), '@bill/index', ['class' => 'small-box-footer']) ?>
    <?php if (Yii::$app->user->can('bill.create')) : ?>
        <?= Html::a(Yii::t('hipanel', 'Create') . $box->icon('fa-plus'), '@bill/create', ['class' => 'small-box-footer']) ?>
    <?php endif ?>
    <?php if (Yii::$app->user->can('deposit')) : ?>
        <?= Html::a(Yii::t('hipanel', 'Recharge') . $box->icon('fa-credit-card-alt'), '@pay/deposit', ['class' => 'small-box-footer']) ?>
    <?php endif ?>
    <?php $box->endFooter() ?>
    <?php $box::end() ?>
</div>
