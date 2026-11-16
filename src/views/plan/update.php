<?php

use hipanel\modules\finance\controllers\PlanController;
use hipanel\modules\finance\models\Plan;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var Plan $models
 * @var Plan $model
 */

$this->title = Yii::t('hipanel', 'Update');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Tariff plans'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Html::encode($model->name), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<?php foreach (Yii::$app->session->getFlash(PlanController::FORK_ERROR_FLASH, [], true) as $message) : ?>
    <div class="alert alert-danger">
        <strong><?= Yii::t('hipanel.finance.plan', 'The plan was not forked. Fix the custom attributes below and fork it again.') ?></strong>
        <br>
        <?= nl2br(Html::encode($message)) ?>
    </div>
<?php endforeach ?>

<?= $this->render('_form', ['models' => $models, 'model' => $model]) ?>
