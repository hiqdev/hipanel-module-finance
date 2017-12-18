<?php

use hipanel\helpers\Url;
use hipanel\modules\client\models\ClientSearch;
use hipanel\modules\dashboard\widgets\SearchForm;
use hipanel\modules\dashboard\widgets\SmallBox;
use yii\helpers\Html;

?>

<div class="col-lg-3 col-md-6 col-sm-12 col-xs-12">
    <?php $box = SmallBox::begin([
        'boxTitle' => Yii::t('hipanel:finance', 'Tariffs'),
        'boxIcon' => 'fa-usd',
        'boxColor' => SmallBox::COLOR_GREEN,
    ]) ?>
    <?php $box->beginBody() ?>
    <br>
    <br>
    <?= SearchForm::widget([
        'formOptions' => [
            'id' => 'client-search',
            'action' => Url::to('@client/index'),
        ],
        'model' => new ClientSearch(),
        'attribute' => 'tariff_like',
        'buttonColor' => SmallBox::COLOR_GREEN,
    ]) ?>
    <?php $box->endBody() ?>
    <?php $box->beginFooter() ?>
    <?= Html::a(Yii::t('hipanel', 'View') . $box->icon(), '@tariff/index', ['class' => 'small-box-footer']) ?>
    <?php $box->endFooter() ?>
    <?php $box::end() ?>
</div>
