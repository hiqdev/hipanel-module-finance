<?php

/**
 * @var $manager \hipanel\modules\finance\logic\AbstractTariffManager
 * @var $this \yii\web\View
 */

use hipanel\modules\finance\menus\TariffDetailMenu;
use hipanel\widgets\Box;
use hipanel\widgets\ClientSellerLink;
use hiqdev\menumanager\widgets\DetailMenu;
use yii\helpers\Html;

$model = $manager->form;
$type = $manager->getType();

$this->title = Html::encode($model->name);
$this->params['subtitle'] = Yii::t('hipanel:finance:tariff', 'tariff detailed information');
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
        <?php if (Yii::$app->user->can('manage')) : ?>
            <div class="profile-usermenu">
                <?= TariffDetailMenu::create(['model' => $model])->render(DetailMenu::class) ?>
            </div>
        <?php endif ?>
        <?php Box::end() ?>
    </div>
    <div class="col-md-9">
        <?= $this->render($type . '/view', ['model' => $model, 'manager' => $manager]) ?>
    </div>
</div>
