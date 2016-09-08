<?php

/**
 * @var $manager \hipanel\modules\finance\logic\AbstractTariffManager
 * @var $this \yii\web\View
 */

use hipanel\widgets\Box;
use hipanel\widgets\ClientSellerLink;
use hipanel\widgets\ModalButton;
use hipanel\widgets\Pjax;
use yii\helpers\Html;

$model = $manager->form;
$type = $manager->getType();

$this->title = Html::encode($model->name);
$this->params['subtitle'] = Yii::t('hipanel/finance/tariff', 'tariff detailed information');
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
                <ul class="nav">
                    <li>
                        <?= Html::a('<i class="ion-edit"></i>' . Yii::t('hipanel', 'Update'), ['update', 'id' => $model->id]) ?>
                    </li>
                    <li>
                        <?= ModalButton::widget([
                            'model'    => $model->getTariff(),
                            'scenario' => 'delete',
                            'button'   => ['label' => '<i class="fa fa-fw fa-trash-o"></i>' . Yii::t('hipanel', 'Delete')],
                            'body'     => Yii::t('hipanel/finance/tariff', 'Tariff must be unlinked form all objects before. Are you sure you want to delete tariff {name}?', ['name' => $model->name]),
                            'modal'    => [
                                'header'        => Html::tag('h4', Yii::t('hipanel/finance/tariff', 'Confirm tariff deleting')),
                                'headerOptions' => ['class' => 'label-danger'],
                                'footer'        => [
                                    'label'             => Yii::t('hipanel/finance/tariff', 'Delete tariff'),
                                    'data-loading-text' => Yii::t('hipanel/finance/tariff', 'Deleting tariff...'),
                                    'class'             => 'btn btn-danger',
                                ]
                            ]
                        ]);

                        ?>
                    </li>
                </ul>
            </div>
        <?php endif ?>
        <?php Box::end() ?>
    </div>
    <div class="col-md-9">
        <?= $this->render($type . '/view', ['model' => $model]) ?>
    </div>
</div>
