<?php

/**
 * @var $manager \hipanel\modules\finance\logic\TariffManager
 * @var $this \yii\web\View
 */

use hipanel\widgets\Box;
use hipanel\widgets\ClientSellerLink;
use hipanel\widgets\Pjax;
use yii\helpers\Html;

$tariffForm = $manager->model;
$type = $manager->getType();

$this->title = Html::encode($tariffForm->name);
$this->subtitle = Yii::t('hipanel/finance/tariff', 'tariff detailed information');
$this->breadcrumbs[] = ['label' => Yii::t('hipanel', 'Tariffs'), 'url' => ['index']];
$this->breadcrumbs[] = $this->title;
?>

<?php


Pjax::begin(Yii::$app->params['pjax']);
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
        ]); ?>
        <div class="profile-user-img text-center">
            <i class="fa fa-dollar fa-5x"></i>
        </div>
        <p class="text-center">
            <span class="profile-user-name">
                <?= Html::encode($tariffForm->name) ?>
                <br/>
                <?= ClientSellerLink::widget(['model' => $tariffForm->model]) ?>
            </span>
            <br>
            <span class="profile-user-role"></span>
        </p>
        <?php if (Yii::$app->user->can('manage')) : ?>
            <div class="profile-usermenu">
                <ul class="nav">
                    <li>
                        <?= Html::a('<i class="ion-edit"></i>' . Yii::t('hipanel', 'Update'), '#'); ?>
                    </li>
                    <li>
                        <?= Html::a('<i class="ion-trash-a"></i>' . Yii::t('hipanel', 'Delete'), '#'); ?>
                    </li>
                </ul>
            </div>
        <?php endif; ?>
        <?php Box::end(); ?>
    </div>
    <div class="col-md-9">
        <?= $this->render($type . '/view', ['tariffForm' => $tariffForm]) ?>
    </div>
</div>
<?php Pjax::end() ?>
