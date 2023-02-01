<?php

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

<?= $this->render('_form', ['models' => $models, 'model' => $model]) ?>
