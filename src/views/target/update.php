<?php

use yii\helpers\Html;
use hipanel\modules\finance\models\Target;

/** @var Target $model */
/** @var Target[] $models */

$this->title = Yii::t('hipanel:finance', 'Update');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Targets'), 'url' => ['index']];
if (count($models) === 1) {
    $this->params['breadcrumbs'][] = ['label' => Html::encode($model->name), 'url' => ['view', 'id' => $model->id]];
}
$this->params['breadcrumbs'][] = $this->title;

?>

<?= $this->render('_form', ['models' => $models, 'model' => $model]) ?>