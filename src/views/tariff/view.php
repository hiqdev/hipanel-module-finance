<?php

/**
 * @var \yii\web\View
 * @var $manager \hipanel\modules\finance\logic\AbstractTariffManager
 */
use hipanel\modules\finance\menus\TariffDetailMenu;
use hipanel\widgets\Box;
use hipanel\widgets\ClientSellerLink;
use yii\helpers\Html;

$model = $manager->form;
$type = $manager->getType();

$this->title = Html::encode($model->name);
$this->params['subtitle'] = Yii::t('hipanel:finance:tariff', 'tariff detailed information');
$this->params['subtitle'] .= ' ' . Html::a(
    Yii::t('hipanel:finance:tariff', 'View as plan'),
    ['@plan/view', 'id' => $model->id],
    ['class' => 'btn btn-xs btn-info']
);

$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel', 'Tariffs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-md-3">
        <?php Box::begin([
            'options' => [
                'class' => 'box-solid',
            ],
            'bodyOptions' => [
                'class' => 'no-padding',
            ],
        ]) ?>
        <div class="profile-user-img text-center">
            <i class="fa fa-dollar fa-5x"></i>
        </div>
        <p class="text-center">
            <span class="profile-user-name">
                <?= Html::encode($model->name) ?>
                <br/>
                <?= ClientSellerLink::widget(['model' => $model->tariff]) ?>
            </span>
            <br>
            <span class="profile-user-role"></span>
        </p>
        <div class="profile-usermenu">
            <?php if ($model->tariff->note) : ?>
                <p style="padding: 10px 15px; border-bottom: 1px solid #f0f4f7;">
                    <?php if (Yii::$app->user->can('plan.set-note')) : ?>
                        <?= Yii::t('hipanel:finance:tariff', '{0}:', [Html::tag('b', $model->tariff->getAttributeLabel('note'))]) ?>
                        <?= \hipanel\widgets\XEditable::widget([
                            'model' => $model->tariff,
                            'attribute' => 'note',
                            'scenario' => 'set-note',
                        ]) ?>
                    <?php else : ?>
                        <?= Yii::t('hipanel:finance:tariff', '{0}: {1}', [Html::tag('b', $model->tariff->getAttributeLabel('note')), Html::encode($model->tariff->note)]) ?>
                    <?php endif; ?>
                </p>
            <?php endif; ?>
            <?php if (Yii::$app->user->can('plan.update')) : ?>
                <?= TariffDetailMenu::widget(['model' => $model]) ?>
            <?php endif ?>
        </div>
        <?php Box::end() ?>
    </div>
    <div class="col-md-9">
        <?= $this->render($type . '/view', ['model' => $model, 'manager' => $manager]) ?>
    </div>
</div>
