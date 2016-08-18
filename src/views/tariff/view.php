<?php

use hipanel\widgets\Box;
use hipanel\widgets\ClientSellerLink;
use hipanel\widgets\Pjax;
use yii\helpers\Html;

$this->title         = Html::encode($model->tariff);
$this->subtitle      = 'tariff detailed information';
$this->breadcrumbs[] = ['label' => Yii::t('hipanel', 'Tariffs'), 'url' => ['index']];
$this->breadcrumbs[] = $this->title;
?>

<?php Pjax::begin(Yii::$app->params['pjax']) ?>
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
            <span class="profile-user-name"><?php printf('%s<br>%s', Html::encode($model->name), ClientSellerLink::widget(compact('model'))) ?></span>
            <br>
            <span class="profile-user-role"></span>
        </p>
        <?php if (Yii::$app->user->can('manage')) : ?>
        <div class="profile-usermenu">
            <ul class="nav">
                <li>
                    <?= Html::a('<i class="ion-edit"></i>' . Yii::t('hipanel', 'Update'), ['update', 'id' => $model->id]); ?>
                </li>
                <li>
                    <?= Html::a('<i class="ion-trash-a"></i>' . Yii::t('hipanel', 'Delete'), ['delete', 'id' => $model->id]); ?>
                </li>
            </ul>
        </div>
        <?php endif; ?>
        <?php Box::end(); ?>
    </div>
</div>
<?php Pjax::end() ?>
