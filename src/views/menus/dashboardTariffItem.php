<?php

use hipanel\helpers\Url;
use hipanel\modules\dashboard\widgets\ObjectsCountWidget;
use hipanel\modules\dashboard\widgets\SearchForm;
use hipanel\modules\dashboard\widgets\SmallBox;
use hipanel\modules\finance\models\PlanSearch;
use yii\helpers\Html;

/** @var string $entityName */
/** @var int $ownCount */

?>

<div class="col-lg-3 col-md-6 col-sm-12 col-xs-12">
    <?php $box = SmallBox::begin([
        'boxTitle' => Yii::t('hipanel:finance', 'Tariffs'),
        'boxIcon' => 'fa-usd',
        'boxColor' => SmallBox::COLOR_GREEN,
    ]) ?>
    <?php $box->beginBody() ?>
    <?= ObjectsCountWidget::widget(compact('route', 'entityName', 'ownCount')) ?>
    <?= SearchForm::widget([
        'formOptions' => [
            'id' => 'plan-search',
            'action' => Url::to('@finance/plan/index'),
        ],
        'model' => new PlanSearch(),
        'attribute' => 'name_ilike',
        'buttonColor' => SmallBox::COLOR_GREEN,
    ]) ?>
    <?php $box->endBody() ?>
    <?php $box->beginFooter() ?>
    <?= Html::a(Yii::t('hipanel', 'View') . $box->icon(), '@plan/index', ['class' => 'small-box-footer']) ?>
    <?php if (Yii::$app->user->can('plan.create')) : ?>
        <?= Html::a(Yii::t('hipanel', 'Create') . $box->icon('fa-plus'), '@plan/create', ['class' => 'small-box-footer']) ?>
    <?php endif ?>
    <?php $box->endFooter() ?>
    <?php $box::end() ?>
</div>
