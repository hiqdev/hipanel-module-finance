<?php

use hipanel\modules\finance\models\Sale;
use yii\helpers\Html;

/** @var Sale $model */
/** @var Sale[] $models */

$this->title = Yii::t('hipanel:finance:sale', 'Edit');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance:sale', 'Sales'), 'url' => ['index']];
if (count($models) === 1) {
    $this->params['breadcrumbs'][] = ['label' => Html::encode($model->object), 'url' => ['view', 'id' => $model->id]];
}
$this->params['breadcrumbs'][] = $this->title;

?>

<?= $this->render('_form', compact('models', 'model')) ?>
